#!/usr/bin/env bash

error(){
  printf "\033[31m%s\n\033[0m" "$@" >&2
  exit 1
}

FORCE_ENVIRONMENT_PLAYBOOK=${FORCE_ENVIRONMENT_PLAYBOOK:-true}

# varstrap
##########

REPO_ROOT=${REPO_ROOT:-"$(git rev-parse --show-toplevel)"}
WORKING_BRANCH=$(git rev-parse --abbrev-ref HEAD)
SKELVARS="$REPO_ROOT/.skelvars"
source $SKELVARS || error "unable to source skelvars. has skel been attached?"

[ -z "$WORKING_BRANCH" ] && error "unable to determine WORKING_BRANCH"
[ -z "$SKEL_DIR" ] && error "SKEL_DIR not defined" "has skel been attached?"
[ -z "$SKEL_REMOTE" ] && error "SKEL_REMOTE not defined" "has skel been attached?"
[ -z "$SKEL_BRANCH" ] && error "SKEL_BRANCH not defined" "has skel been attached?"

WEBROOT_DIR=${WEBROOT_DIR:-webroot}
ENV_DIR="$REPO_ROOT/$SKEL_DIR/env"
ANSIBLE_DIR="$REPO_ROOT/$SKEL_DIR/ansible"
BOILERPLATE_DIR="$REPO_ROOT/$SKEL_DIR/boilerplate"
TESTS_DIR="$REPO_ROOT/tests"
APP_ROOT="$REPO_ROOT/$WEBROOT_DIR"

[ -d "$APP_ROOT" ] || error "APP_ROOT $APP_ROOT doesn't exist"
[ -d "$ENV_DIR" ] || error "ENV_DIR $ENV_DIR doesn't exist"
[ -d "$ANSIBLE_DIR" ] || error "ANSIBLE_DIR $ANSIBLE_DIR doesn't exist"

# attempt to remember initialized values
if [ -f "$ENV_DIR/appvars" ]; then
  grep @CLIENT_CODE $ENV_DIR/appvars >/dev/null 2>&1 || \
    source $ENV_DIR/appvars
  [ -z "$REPO_REMOTE" ] || REPO_REMOTE_NAME=$(git remote -v | grep $REPO_REMOTE | head -n 1 | awk '{print $1}')
fi

REPO_REMOTE_NAME=${REPO_REMOTE_NAME:-origin}

# sed_inplace : in place file substitution
############################################
#
# usage: sed_inplace "file" "sed substitution"
#    ex: sed_inplace "/tmp/file" "s/CLIENT_CODE/BA/g"
#

sed_inplace(){
  # linux
  local SED_CMD="sed"

  if [[ $OSTYPE == darwin* ]]; then
    if $(type gsed >/dev/null 2>&1); then
      local SED_CMD="gsed"
    elif $(type /usr/local/bin/sed >/dev/null 2>&1); then
      local SED_CMD="/usr/local/bin/sed"
    else
      sed -i '' -E "$2" $1
      return
    fi
  fi

  $SED_CMD -r -i "$2" $1
}


# line_in_file : ensure a line exists in a file
###############################################
#
# usage: line_in_file "file" "match" "line"
#    ex: line_in_file "varsfile" "^VARNAME=.*$" "VARNAME=value"
#

line_in_file(){
  local delim=${4:-"|"}
  grep -q "$2" $1 2>/dev/null && sed_inplace $1 "s$delim$2$delim$3$delim" || echo $3 >> $1
}

prompt_confirm() {
  while true; do
    read -r -n 1 -p "${1:-Continue?} [y/n]: " REPLY
    case $REPLY in
      [yY]) echo ; return 0 ;;
      [nN]) echo ; return 1 ;;
      *) printf " \033[31m %s \n\033[0m" "invalid input"
    esac
  done
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
  eval $(docker-machine env $1)
  [ "$(docker-machine active)" = "$1" ] || error "unable to configure $1 machine"
  docker info || error "unable to communicate with $1 machine"
}


env_bootstrap(){

  if [ -z "$ENV" ]; then
    printf "\nno environment passed! pass --help for a list of options.\n\n"
    $REPO_ROOT/bin/env list || error "unable to lookup available environments"
    read -p "Which Environment?  : " ENV
    echo
  fi

  [ -d "$ENV_DIR/$ENV" ] || error "$ENV environment not found"

  if [[ $ENV == qa-* ]]; then
    ANSIBLE_INV_SCRIPT=$ANSIBLE_DIR/inventory/qa_hosts.py
    FORCE_ENVIRONMENT_PLAYBOOK=false
    ANSIBLE_PLAYBOOK="qa.$ANSIBLE_PLAYBOOK"

    # force activation of configured machine
    case $ENV in
      qa-1) configure_docker_machine node-a ;;
      qa-2) configure_docker_machine node-b ;;
      qa-3) configure_docker_machine node-z ;;
      *)    error "no docker-machine has been configured for $ENV" ;;
    esac

  else
    [ -e "$ENV_DIR/$ENV/ssh_config" ] || error "$ENV has no ssh_config"
  fi

}

env_bootstrap_ansible(){
  env_bootstrap

  ANSIBLE_INV_SCRIPT=${ANSIBLE_INV_SCRIPT:-$ANSIBLE_DIR/inventory/ssh_hosts.py}

  [ -e "$ANSIBLE_INV_SCRIPT" ] || error "missing inventory script:" \
    "$ANSIBLE_INV_SCRIPT"

  [ -z "$REPO_REMOTE_NAME" ] && error "REPO_REMOTE_NAME not defined" \
    "has REPO_REMOTE been defined in appvars?"

  export ANSIBLE_SSH_CONF_FILE="$ENV_DIR/$ENV/ssh_config"
  export ANSIBLE_SSH_CONF_ENVARS="$ENV_DIR/appvars,$ENV_DIR/$ENV/envars"
  export ANSIBLE_SSH_CONF_HOSTGROUP="$ENV"

  # cd to ansible playbook directory so we can use host_vars, group_vars
  #  http://docs.ansible.com/ansible/intro_inventory.html
  cd $ANSIBLE_DIR

  if [ -e ${ENV}.${ANSIBLE_PLAYBOOK} ]; then
    ANSIBLE_PLAYBOOK="${ENV}.${ANSIBLE_PLAYBOOK}"
  else
    $FORCE_ENVIRONMENT_PLAYBOOK && error "missing playbook" \
      "${ENV}.${ANSIBLE_PLAYBOOK}"
  fi

  [ -e "$ANSIBLE_PLAYBOOK" ] || error "missing playbook(s)" \
    "$ANSIBLE_PLAYBOOK" "${ENV}.${ANSIBLE_PLAYBOOK}"
}


ansible_command(){

  PROMPT=true
  ANSIBLE_LIST_HOSTS=false
  ANSIBLE_LIST_TAGS=false

  while [ $# -ne 0 ]; do
    case $1 in
      -h|--help|help)     ansible_command_help ;;
      --no-prompt|-y)     PROMPT=false ;;
      --limit|-l)         ANSIBLE_LIMIT="--limit=$2" ; shift ;;
      --list-hosts)       ANSIBLE_LIST_HOSTS=true ;;
      --tags|-t)          ANSIBLE_TAGS="--tags=$2" ; shift ;;
      --list-tags)        ANSIBLE_LIST_TAGS=true ;;
      --extra-vars|-e)    ANSIBLE_VARS="$2" ; shift ;;
      --inventory-script) ANSIBLE_INV_SCRIPT="$2" ; shift ;;
      --step|-s)          ANSIBLE_STEP="--step" ;;
      --private-key)      ANSIBLE_KEY="--private-key=$2" ; shift ;;
      --verbose|-v)       ANSIBLE_VERBOSITY="-v" ;;
      --dry-run|-d)       ANSIBLE_CHECK="--check --diff" ;;
      --ask-pass|-p)      ANSIBLE_ASK_SSH_PASS="--ask-pass" ;;
      --ask-sudo|-sp)     ANSIBLE_ASK_SUDO_PASS="--ask-become-pass" ;;
      -vvv)               ANSIBLE_VERBOSITY="-vvvv" ;;
      *)                  if [[ $1 == -* ]]; then
                            echo "invalid option: $1" ; ansible_command_help 1
                          fi
                          ENV="$1"
                          ;;
    esac
    shift
  done

  env_bootstrap_ansible

  if $ANSIBLE_LIST_TAGS ; then
    ansible-playbook -i $ANSIBLE_INV_SCRIPT $ANSIBLE_PLAYBOOK --list-tags
    exit 0
  fi

  if $ANSIBLE_LIST_HOSTS ; then
    ansible-playbook -i $ANSIBLE_INV_SCRIPT $ANSIBLE_PLAYBOOK --list-hosts
    exit 0
  fi

  if $PROMPT ; then
    echo
    printf "environment: \033[35m%s\n\033[0m" "$ENV"

    ansible-playbook -i $ANSIBLE_INV_SCRIPT $ANSIBLE_PLAYBOOK \
      --list-hosts $ANSIBLE_LIMIT

    type PROMPT_FN >/dev/null 2>&1 && PROMPT_FN

    prompt_confirm || exit 0;
  fi

  export ANSIBLE_HOST_KEY_CHECKING=false

  ansible-playbook -i $ANSIBLE_INV_SCRIPT $ANSIBLE_PLAYBOOK  \
    $ANSIBLE_TAGS $ANSIBLE_LIMIT $ANSIBLE_VERBOSITY $ANSIBLE_CHECK \
    $ANSIBLE_ASK_SSH_PASS $ANSIBLE_ASK_SUDO_PASS $ANSIBLE_STEP \
    --extra-vars="ENV=$ENV LOCAL_ROOT=$REPO_ROOT REPO_REMOTE_NAME=$REPO_REMOTE_NAME $ANSIBLE_VARS"
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
