#!/usr/bin/env bash
__cmd_prefix="badevops-"
. $(dirname $0)/.shell-helpers.sh &>/dev/null || {
  echo "ERROR - unable to load shell-helpers.sh!"
  exit 1
}

# varstrap
##########

REPO_ROOT=${REPO_ROOT:-"$(git rev-parse --show-toplevel)"}
WEBROOT_DIR=${WEBROOT_DIR:-webroot}
WORKING_BRANCH=$(git rev-parse --abbrev-ref HEAD)
SKEL_IN_GROUNDCONTROL=false

. "$REPO_ROOT/.skelvars" || error \
  "unable to source skelvars. has skel been attached?"

[ -z "$WORKING_BRANCH" ] && error "unable to determine WORKING_BRANCH"
[ -z "$SKEL_DIR" ] && error "SKEL_DIR not defined" "has skel been attached?"
[ -z "$SKEL_REMOTE" ] && error "SKEL_REMOTE not defined" "has skel been attached?"
[ -z "$SKEL_BRANCH" ] && error "SKEL_BRANCH not defined" "has skel been attached?"

WEBROOT_DIR=${WEBROOT_DIR:-webroot}
ENV_DIR="$REPO_ROOT/$SKEL_DIR/env"
ANSIBLE_DIR="$REPO_ROOT/$SKEL_DIR/ansible"
DOCKER_DIR="$REPO_ROOT/$SKEL_DIR/docker"
BOILERPLATE_DIR="$REPO_ROOT/$SKEL_DIR/boilerplate"
TESTS_DIR="$REPO_ROOT/tests"
APP_ROOT="$REPO_ROOT/$WEBROOT_DIR"
SKIP_BOOTSTRAP=false

[ -d "$APP_ROOT" ] || error "APP_ROOT $APP_ROOT doesn't exist"
[ -d "$ENV_DIR" ] || error "ENV_DIR $ENV_DIR doesn't exist"
[ -d "$ANSIBLE_DIR" ] || error "ANSIBLE_DIR $ANSIBLE_DIR doesn't exist"

# attempt to remember initialized values
if [ -f "$ENV_DIR/appvars" ]; then
  grep @CLIENT_CODE $ENV_DIR/appvars >/dev/null 2>&1 || \
    . $ENV_DIR/appvars
  [ -z "$REPO_REMOTE" ] || REPO_REMOTE_NAME=$(git remote -v | grep $REPO_REMOTE | head -n 1 | awk '{print $1}')
fi

REPO_REMOTE_NAME=${REPO_REMOTE_NAME:-origin}


#
# helpers
#

# from shell-helpers v2 -@TODO refactor in devops-prototype-skel
is/absolute(){
  [[ "${1:0:1}" == / || "${1:0:2}" == ~[/a-z] ]]
}

# modified from git-subtree.sh, sets LATEST_SQUASH var to tree ref
#  http://git.kernel.org/cgit/git/git.git/tree/contrib/subtree/git-subtree.sh
find_latest_squash()
{
  dir=$1
  sq=
	main=
	sub=
	git log --grep="^git-subtree-dir: $dir/*\$" \
		--pretty=format:'START %H%n%s%n%n%b%nEND%n' HEAD |
	while read a b junk; do
		case "$a" in
			START) sq="$b" ;;
			git-subtree-mainline:) main="$b" ;;
			git-subtree-split:)
				sub="$(git rev-parse "$b^0")" ||
				    error "could not rev-parse split hash $b from commit $sq"
				;;
			END)
				if [ -n "$sub" ]; then
					if [ -n "$main" ]; then
						sq="$sub"
					fi
          echo "LAST_SQUASH=$sq"
          echo "LAST_SQUASH_TREE=$sub"
          return 0
					break
				fi
				sq=
				main=
				sub=
				;;
		esac
	done
  return 1
}


configure_docker_machine(){
  if [ $1 = "local" ]; then
    echo "local flag detected. de-activating docker-machine if set..."
    __deactivate_machine
  else
    local SHELL=${SHELL:-"sh"}
    echo "activing docker-machine $1..."
    eval $(docker-machine env $1 --shell $SHELL)
    [ "$(docker-machine active)" = "$1" ] || error "unable to configure $1 machine"
    docker info || error "unable to communicate with $1 machine"
  fi
}

fail_in_groundcontrol(){
  if $SKEL_IN_GROUNDCONTROL; then
    local errmsg="you cannot run this from groundcontrol"
    [ -z "$@" ] || errmsg="you cannot run '$@' from groundcontrol"
    error "$errmsg" "it probably requres WEBROOT_DIR!"
  fi
}

env_bootstrap(){

  if [ -z "$ENV" ]; then
    printf "\nno environment passed! pass --help for a list of options.\n\n"
    $REPO_ROOT/$SKEL_DIR/bin/env list || error "unable to lookup available environments"
    read -p "Which Environment?  : " ENV
    echo
  fi

  [ -d "$ENV_DIR/$ENV" ] || error "$ENV environment not found"

  DOCKER_MACHINE=$($REPO_ROOT/$SKEL_DIR/bin/env var DOCKER_MACHINE $ENV)
}

env_bootstrap_ansible(){
  env_bootstrap

  local envplaybook=false

  # prefer per-environment playbook if exists
  [ -e "${ANSIBLE_DIR}/${ENV}.${ANSIBLE_PLAYBOOK}" ] && {
    ANSIBLE_PLAYBOOK="$ANSIBLE_DIR/${ENV}.${ANSIBLE_PLAYBOOK}"
    envplaybook=true
  }


  if [ ! -z "$DOCKER_MACHINE" ]; then

    #
    # dockerized environments
    #

    ANSIBLE_INV_SCRIPT=${ANSIBLE_INV_SCRIPT:-$ANSIBLE_DIR/inventory/dockerized_hosts.py}
    FORCE_ENVIRONMENT_PLAYBOOK=false
    $envplaybook || ANSIBLE_PLAYBOOK="dockerized.$ANSIBLE_PLAYBOOK"

    # force activation of configured machine
    export MACHINE_STORAGE_PATH=${MACHINE_STORAGE_PATH:-~/.docker/machine}
    configure_docker_machine $DOCKER_MACHINE

  else
    #
    # legacy environments
    #


    ANSIBLE_INV_SCRIPT=${ANSIBLE_INV_SCRIPT:-$ANSIBLE_DIR/inventory/ssh_hosts.py}
    FORCE_ENVIRONMENT_PLAYBOOK=${FORCE_ENVIRONMENT_PLAYBOOK:-false}

    [ -e "$ENV_DIR/$ENV/ssh_config" ] || error "$ENV has no ssh_config"
  fi

  $FORCE_ENVIRONMENT_PLAYBOOK && ! $envplaybook && error \
    "missing per-environment playbook" "${ENV}.${ANSIBLE_PLAYBOOK}"

  is/absolute "$ANSIBLE_PLAYBOOK" || \
    ANSIBLE_PLAYBOOK="$ANSIBLE_DIR/$ANSIBLE_PLAYBOOK"

  [ -e "$ANSIBLE_PLAYBOOK" ] || error "missing playbook: $ANSIBLE_PLAYBOOK"


  [ -e "$ANSIBLE_INV_SCRIPT" ] || error "missing inventory script:" \
    "$ANSIBLE_INV_SCRIPT"

  [ -z "$REPO_REMOTE_NAME" ] && error "REPO_REMOTE_NAME not defined" \
    "has REPO_REMOTE been defined in appvars?"

  export ANSIBLE_SSH_CONF_FILE="$ENV_DIR/$ENV/ssh_config"
  export ANSIBLE_SSH_CONF_ENVARS="$REPO_ROOT/$SKEL_DIR/.skelvars,$ENV_DIR/appvars,$ENV_DIR/$ENV/envars"
  export ANSIBLE_SSH_CONF_HOSTGROUP="$ENV"

  if $DEBUG; then
    echo export ANSIBLE_SSH_CONF_FILE="$ENV_DIR/$ENV/ssh_config"
    echo export ANSIBLE_SSH_CONF_ENVARS="$REPO_ROOT/$SKEL_DIR/.skelvars,$ENV_DIR/appvars,$ENV_DIR/$ENV/envars"
    echo export ANSIBLE_SSH_CONF_HOSTGROUP="$ENV"
  fi

}


ansible_command(){

  PROMPT=true
  DEBUG=false
  ANSIBLE_LIST_HOSTS=false
  ANSIBLE_LIST_TAGS=false
  ANSIBLE_VARS=""
  SKELWRAP=${SKELWRAP:-false}

  # run ansible commands within the badevops-skelwrap container's environment
  $SKELWRAP && [ ! "$DEX_IMAGE_NAME" = "skelwrap" ] && {
    echo "execing badevops-skelwrap $0 $@"
    exec badevops-skelwrap $0 $@
  }

  while [ $# -ne 0 ]; do
    case $1 in
      -h|--help|help)     ansible_command_help ;;
      --no-prompt|-y)     PROMPT=false ;;
      --limit|-l)         ANSIBLE_LIMIT="--limit=$2" ; shift ;;
      --list-hosts)       ANSIBLE_LIST_HOSTS=true ;;
      --tags|-t)          ANSIBLE_TAGS="--tags=$2" ; shift ;;
      --list-tags)        ANSIBLE_LIST_TAGS=true ;;
      --extra-vars|-e)    ANSIBLE_VARS+=" -e $2" ; shift ;;
      --inventory-script) ANSIBLE_INV_SCRIPT="$2" ; shift ;;
      --step|-s)          ANSIBLE_STEP="--step" ;;
      --private-key)      ANSIBLE_KEY="--private-key=$2" ; shift ;;
      --verbose|-v)       ANSIBLE_VERBOSITY="-v" ;;
      --dry-run|-d)       ANSIBLE_CHECK="--check --diff" ;;
      --ask-pass|-p)      ANSIBLE_ASK_SSH_PASS="--ask-pass" ;;
      --ask-sudo|-sp)     ANSIBLE_ASK_SUDO_PASS="--ask-become-pass" ;;
      -vvv)               ANSIBLE_VERBOSITY="-vvvv" ;;
      --debug)            DEBUG=true ;;
      *)                  if [[ $1 == -* ]]; then
                            echo "invalid option: $1" ; ansible_command_help 1
                          fi
                          ENV="$1"
                          ;;
    esac
    shift
  done

  env_bootstrap_ansible

  set_cmd ansible-playbook || error \
    "ansible-playbook is required! please initialize badevops-bootstrap"

  $SKIP_BOOTSTRAP && __cmd=ansible-playbook

  cd $REPO_ROOT

  if $ANSIBLE_LIST_TAGS ; then
    $__cmd -i $ANSIBLE_INV_SCRIPT $ANSIBLE_PLAYBOOK --list-tags
    exit 0
  fi

  if $ANSIBLE_LIST_HOSTS ; then
    $__cmd -i $ANSIBLE_INV_SCRIPT $ANSIBLE_PLAYBOOK --list-hosts
    exit 0
  fi

  if $DEBUG; then
    echo "$__cmd -i $ANSIBLE_INV_SCRIPT $ANSIBLE_PLAYBOOK  \
    $ANSIBLE_TAGS $ANSIBLE_LIMIT $ANSIBLE_VERBOSITY $ANSIBLE_CHECK \
    $ANSIBLE_ASK_SSH_PASS $ANSIBLE_ASK_SUDO_PASS $ANSIBLE_STEP \
    --extra-vars=\"ENV=$ENV LOCAL_ROOT=$REPO_ROOT REPO_REMOTE_NAME=$REPO_REMOTE_NAME $ANSIBLE_VARS\""
    echo
    prompt_confirm "DEBUG -- about to run above commands. continue?" || exit 0;
  fi

  if $PROMPT ; then
    echo
    printf "environment: \033[35m%s\n\033[0m" "$ENV"

    $__cmd -i $ANSIBLE_INV_SCRIPT $ANSIBLE_PLAYBOOK \
      --list-hosts $ANSIBLE_LIMIT

    type PROMPT_FN >/dev/null 2>&1 && PROMPT_FN

    prompt_confirm || exit 0;
  fi

  export ANSIBLE_HOST_KEY_CHECKING=false
  export ANSIBLE_RETRY_FILES_ENABLED=false

  $__cmd -i $ANSIBLE_INV_SCRIPT $ANSIBLE_PLAYBOOK  \
    $ANSIBLE_TAGS $ANSIBLE_LIMIT $ANSIBLE_VERBOSITY $ANSIBLE_CHECK \
    $ANSIBLE_ASK_SSH_PASS $ANSIBLE_ASK_SUDO_PASS $ANSIBLE_STEP \
    -e ENV=$ENV -e LOCAL_ROOT=$REPO_ROOT -e REPO_REMOTE_NAME=$REPO_REMOTE_NAME $ANSIBLE_VARS
}

ansible_command_help() {
  cat <<-EOF

  $CMD_HELP_PREFIX

  Arguments:
    environment          Environment to run against


  Options:
    -l | --limit         Limit hosts to pattern. See Ansible Patterns.
                         Use to limit hosts to execute upon.

    --list-hosts         List hosts effected by playbook.

    -t | --tags          Tags to pass to ansible playbook. Comma separated list.
                         Use to limit which tasks are run.

    --list-tags          List available tags in playbook.

    -d | --dry-run       Preview Changes -- don't make any. We recommend using
                         this in combination with limit to reduce output.

    -e | --extra-vars    Set/override vars used by ansible playbook.

    --private-key        Path to private keyfile used for authentication

    -p | --ask-pass      Ask for SSH password used to connection

    -sp | --ask-sudo     Ask for sudo password

    -y | --no-prompt     Do not prompt for confirmation

    -s | --step          Execute interactively, prompting before each task

    --verbose | -v       Verbose Output. Use -vvv for extra verbosity

    --help               Display help


EOF

  if [ $# -eq 0 ]; then
    exit 0
  fi

  exit $1
}
