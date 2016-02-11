#!/bin/sh

error(){
  printf "\033[31m%s\n\033[0m" "$@" >&2
  exit 1
}

env_bootstrap(){

  [ ! -z "$BLUEACORN_DIR" ] || error "BLUEACORN_DIR cannot be empty." \
   "be sure to source the bootstrap environment file, or restart your shell"

  [ -z "$ENV" ] && $REPO_ROOT/env.sh list \
    && read -p "Which Environment? : " ENV
  echo

  ENV_DIR="$REPO_ROOT/skel/env"

  [ -d "$ENV_DIR/$ENV" ] || error "$ENV environment not found"
  [ -e "$ENV_DIR/$ENV/ssh_config" ] || error "$ENV has no ssh_config"
}

env_bootstrap_ansible(){
  env_bootstrap

  ANSIBLE_INV_SCRIPT=${ANSIBLE_INV_SCRIPT:-$BLUEACORN_DIR/ansible/ssh_hosts.py}

  [ -e "$ANSIBLE_INV_SCRIPT" ] || error "missing inventory script:" \
    "$ANSIBLE_INV_SCRIPT"

  export ANSIBLE_SSH_CONF_FILE="$ENV_DIR/$ENV/ssh_config"
  export ANSIBLE_SSH_CONF_ENVARS="$ENV_DIR/envars,$ENV_DIR/$ENV/envars"

  # cd to ansible playbook directory so we can use host_vars, group_vars
  #  http://docs.ansible.com/ansible/intro_inventory.html
  cd $REPO_ROOT/skel/ansible

  ANSIBLE_PLAYBOOK="${ENV}.${ANSIBLE_PLAYBOOK}"
  [ -e "$ANSIBLE_PLAYBOOK" ] || error "missing environment playbook" \
    "$REPO_ROOT/ansible/$ANSIBLE_PLAYBOOK"
}


ansible_command(){

  PROMPT=true
  ANSIBLE_LIST_HOSTS=false
  ANSIBLE_LIST_TAGS=false

  if [ $# -eq 0 ]; then
    ansible_command_help 1
  else
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
        --private-key)      ANSIBLE_KEY="--private-key=$2" ; shift ;;
        --verbose|-v)       ANSIBLE_VERBOSITY="-v" ;;
        --dry-run|-d)       ANSIBLE_CHECK="--check --diff" ;;
        -vvv)               ANSIBLE_VERBOSITY="-vvvv" ;;
        *)                  if [[ $1 == -* ]]; then
                              echo "invalid option: $1" ; ansible_command_help 1
                            fi
                            ENV="$1"
                            ;;
      esac
      shift
    done
  fi

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

    read -p "Continue (y/n) ? " && [[ $REPLY =~ ^[Yy] ]] || exit 0;
  fi

  ansible-playbook -i $ANSIBLE_INV_SCRIPT $ANSIBLE_PLAYBOOK  \
    $ANSIBLE_TAGS $ANSIBLE_LIMIT $ANSIBLE_VERBOSITY $ANSIBLE_CHECK \
    --extra-vars="ENV=$ENV LOCAL_ROOT=$REPO_ROOT $ANSIBLE_VARS"
}

ansible_command_help() {
  cat <<-EOF

  $CMD_HELP_PREFIX

  Arguments:
    environment          Environment to run against


  Options:
    --limit | -l         Limit hosts to pattern. See Ansible Patterns.
                         Use to limit hosts to execute upon.

    --list-hosts         List hosts effected by playbook.

    --tags | -t          Tags to pass to ansible playbook. Comma separated list.
                         Use to limit which tasks are run.

    --list-tags          List available tags in playbook.

    --dry-run | -d       Preview Changes -- don't make any. We recommend using
                         this in combination with limit to reduce output.

    --extra-vars | -e    Set/override vars used by ansible playbook.

    --inventory-file     Path to ansible inventory file
                         (default: $BLUEACORN_DIR/ansible/ssh_hosts.py)

    --private-key        Path to private keyfile used for authentication

    --no-prompt | -y     Do not prompt for confirmation

    --verbose | -v       Verbose Output. Use -vvv for extra verbosity

    --help               Display help


EOF

  if [ $# -eq 0 ]; then
    exit 0
  fi

  exit $1
}
