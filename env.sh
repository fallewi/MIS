#!/usr/bin/env bash

# utility
#########

display_help() {
  cat <<-EOF

  A utility for managing environments

  Usage: env.sh <command> [options]

  Commands:
    set <environment>    set the current environment
                         (environment must be set per machine)

    get                  Print the current environment

    list                 Print the available environments
                         (directories under env/)

    sync [environment]   Links configuration from envfiles, and copies defaults
                         from /skel/webroot to /webroot

    vars [environment]   Prints variables from current [or passed] environment
                         (from envars files)

    make <environment>   Generate a new environment from templates


  Options:
    --help               Display help

    --quiet | -q         semantic output

    --force | -f         Overwrite files when syncing

    --no-prompt | -np    Avoids prompts (skips existing files) when syncing

    --skip-ansible       Skip ansible templates when making an environment

    --skip-env           Skip environment folders when making an environment

    --webroot <webroot>  Directory holding the magento distribution
                         (def: webroot)

EOF

  if [ $# -eq 0 ]; then
    exit 0
  fi

  exit $1
}

error(){
  printf "\033[31m%s\n\033[0m" "$@" >&2
  exit 1
}

# globals
#########

type greadlink >/dev/null 2>&1 && CWD="$(dirname "$(greadlink -f "$0")")" || \
  CWD="$(dirname "$(readlink -f "$0")")"
REPO_ROOT=`git rev-parse --show-toplevel`
ENV_DIR="$REPO_ROOT/skel/env"
ANSIBLE_DIR="$REPO_ROOT/skel/ansible"

# defaults
QUIET=${INIT_QUIET:-false}
WEBROOT=${INIT_WEBROOT:-"webroot"}
FORCE_OVERWRITE=${INIT_FORCE_OVERWRITE:-false}
SKIP_PROMPTS=${INIT_SKIP_PROMPTS:-false}
SKIP_ENV=${SKIP_ENV:-false}
SKIP_ANSIBLE=${SKIP_ANSIBLE:-false}


# run functions
###############

run_task() {
  echo
  echo "  * starting: $2 -->"
  echo

  $1
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

set_env(){

  [ -z "$ENV" ] && print_list && read -p "environment to set: " ENV
  echo


  [ -d "$ENV_DIR/$ENV" ] || error "environment $ENV not found"

  run_task "env_conf" "linking config files"
  run_task "env_mark" "marking environment as '$ENV'"
  [ "$ENV" = "local" ] && run_task "env_local" "extra `local` environment setup"

  printf "\n [+] configured environment: \033[35m %s \n\033[0m\n\n" "$ENV"

}

sync_env(){
  ENV=${ENV:-current}
  [ -d "$ENV_DIR/$ENV" ] || error "environment $ENV not found"

  run_task "env_conf" "linking envfiles configuration"
  run_task "env_sync" "copying webroot defaults from skel/webroot"

}

print_env(){
  if ! $QUIET; then printf "Current environment: "; fi
  [ -e $ENV_DIR/current ] || error "uninitialized"
  type greadlink >/dev/null 2>&1 && READLINK="greadlink" || READLINK="readlink"
  echo $($READLINK "$ENV_DIR/current")
}

print_list(){
  if ! $QUIET; then echo "Available environments: "; fi
  cd $ENV_DIR
  ls -d */ | grep -v "current" | sed 's/\///'
}

print_vars(){

  ENV=${ENV:-current}

  [ -e $ENV_DIR/$ENV ] || error "environment not found"
  [ -e $ENV_DIR/$ENV/envars ] || error "no envars for $ENV environment"

  ! $QUIET && printf "\n\033[35m%s\033[0m vars -->\n\n" "$ENV"

  local CMD="set -a ; \
    source $ENV_DIR/envars ; \
    source $ENV_DIR/$ENV/envars ; \
    printenv | sed '/^\(SHLVL\|PWD\|_\)=/d'"
  env -i sh -c "$CMD"
}


# environment fns
#################

env_conf() {
  link() {
    SRC=$ENV_DIR/$ENV/$1
    DEST=$REPO_ROOT/$1

    if [ ! -e $SRC ]; then
      echo "skipping $1"; return
    fi

    if ! $FORCE_OVERWRITE ; then
      if [ -e $DEST ] || [ -h $DEST ] ; then
        $SKIP_PROMPTS && return
        prompt_confirm "Overwrite $1?" || return
      fi
    fi

    echo "linking $1"
    if [ -e $DEST ] || [ -h $DEST ]; then
     rm $DEST
    elif [ ! -d $(dirname $DEST) ]; then
      mkdir -p $(dirname $DEST)
    fi
    ln -s $SRC $DEST
  }

  local envfiles="$ENV_DIR/$ENV/envfiles"
  [ -e "$envfiles" ] || envfiles="$ENV_DIR/envfiles"
  [ -e "$envfiles" ] || error "envfiles not found!"

  for file in $(cat $envfiles); do
    link "$file"
  done
}


env_sync() {
  file_sync() {
    SRC=$REPO_ROOT/skel/$1
    DEST=$REPO_ROOT/$1

    if ! $FORCE_OVERWRITE ; then
      if [ -e $DEST ] || [ -h $DEST ] ; then
        $SKIP_PROMPTS && return
        prompt_confirm "Overwrite $1?" || return
      fi
    fi

    echo "syncing $1"
    if [ -e $DEST ] || [ -h $DEST ]; then
     rm $DEST
    elif [ ! -d $(dirname $DEST) ]; then
      mkdir -p $(dirname $DEST)
    fi
    cp $SRC $DEST
  }

  cd $REPO_ROOT/skel

  [ -d "webroot" ] || error "skel/webroot does not exist!"

  for file in $(find webroot/ -type f); do
    file_sync "$file"
  done
}





env_mark(){
  cd $ENV_DIR || error "could not change to environment directory"
  [ -h "$ENV_DIR/current" ] && rm current
  ln -s $ENV current || error "failed to mark environment"
}


env_local() {

  echo "[GIT] assuming index.php as unchanged"
  git update-index --assume-unchanged $MAGE_ROOT/index.php

  echo "[GIT] configure core.fileMode as false"
  git config core.fileMode false

  [ -d "$REPO_ROOT/blueacornui" ] && env_blueacornui || \
    echo -e "\n\nskipping blueacornui... blueacornui folder not found\n\n"
}

env_blueacornui() {
  echo
  echo "initializing blueacornui..."
  echo

  [ -d "$REPO_ROOT/blueacornui" ] || \
    error "missing $REPO_ROOT/blueacornui" "has init been run?"

  cd $REPO_ROOT/blueacornui

  if [ ! -d node_modules ]; then
    npm install
    bower install
  fi

  if [ ! -d "$MAGE_ROOT/skin/frontend/blueacorn/site" ]; then
    grunt setup:site
  fi

  echo
  echo "registering githooks with 'grunt dev-githooks'"
  echo
  grunt dev-githooks

  echo
  echo "compiling assets with 'grunt compile'"
  echo
  grunt compile
}

env_make() {

  [ -e $ENV_DIR/envars ] || error "repo appears uninitialized"
  source $ENV_DIR/envars

  source $BLUEACORN_DIR/lib/functions || error \
    "could not source BA functions" "run bootstrap or restart your shell"

  [ -z "$ENV" ] && read -p "environment name to generate: " ENV

  echo "skel environments -->"
  cd $ENV_DIR/.skel
  ls -d */ | grep -v local | sed "s/\///"

  cd $ENV_DIR
  ls -d */ | grep -v local | grep -v current | sed "s/\///"

  printf "\nskel environment to base \033[35m%s\033[0m on: " "$ENV"
  read -r SKEL
  echo

  local BASE_DIR="$ENV_DIR/$SKEL"
  local BASE_TYPE="existing"
  local SED_REPLACE="s|$SKEL|$ENV|g"

  if [ ! -d "$BASE_DIR" ]; then
    local BASE_DIR="$ENV_DIR/.skel/$SKEL"
    local BASE_TYPE="skel"
    local SED_REPLACE="s|@ENV|$ENV|g;s|@CLIENT_CODE|$CLIENT_CODE|g;s|@REPO_REMOTE|$REPO_REMOTE|g;s|@DOMAIN|$DOMAIN|g"
  fi

  [ -d "$BASE_DIR" ] || error "$SKEL is not a valid base environment"

  if ! $SKIP_ENV ; then

    [ -d "$ENV_DIR/$ENV" ] && error "environment \"$ENV\" already exists"

    echo "  generating $ENV from $BASE_TYPE $SKEL ..."
    cp -a $BASE_DIR $ENV_DIR/$ENV

    echo "  activating $ENV ..."
    cd $ENV_DIR/$ENV
    for file in `find * -type f`; do
      sed_inplace "$file" "$SED_REPLACE"
    done
  fi

  if ! $SKIP_ANSIBLE ; then
    [ -d "$ENV_DIR/$ENV" ] || error "environment \"$ENV\" does not exist"

    echo "  generating $ENV ansible templates from $BASE_TYPE $SKEL ..."

    if [ "$BASE_TYPE" = "skel" ]; then
      cd $ANSIBLE_DIR/.skel || error "ansible skel dir missing"
      for file in *; do
        cp $file $ANSIBLE_DIR/${ENV}.$file
        sed_inplace "$ANSIBLE_DIR/${ENV}.$file" "$SED_REPLACE"
      done
    else
      cd $ANSIBLE_DIR || error "ansible dir missing"
      for file in $SKEL.*; do
        local target=$ENV.${file//$SKEL./}
        cp $file $target
        sed_inplace "$target" "$SED_REPLACE"
      done
    fi

  fi

  printf "\n...\033[35m%s\033[0m has been created!\n" "$ENV"
}



# runtime
#########

runstr="display_help"

if [ $# -eq 0 ]; then
  display_help 1
else
  while [ $# -ne 0 ]; do
    case $1 in
      -h|--help|help)    display_help ;;
      --force|-f)        FORCE_OVERWRITE=true ;;
      --no-prompt|-np)   SKIP_PROMPTS=true ;;
      --quiet|-q)        QUIET=true ;;
      --skip-ansible)    SKIP_ANSIBLE=true ;;
      --skip-env)        SKIP_ENV=true ;;
      --webroot)         WEBROOT="$2" ; shift ;;
      list)              runstr="print_list" ;;
      make)              runstr="env_make" ;
                         if [ ! -z "$2" ] && [[ ! $2 == -* ]]; then
                           ENV="$2" ; shift ;
                         fi
                         ;;
      set)               runstr="set_env" ;
                         if [ ! -z "$2" ] && [[ ! $2 == -* ]]; then
                           ENV="$2" ; shift ;
                         fi
                         ;;
      get)               runstr="print_env" ;;
      vars)              runstr="print_vars"
                         if [ ! -z "$2" ] && [[ ! $2 == -* ]]; then
                           ENV="$2" ; shift ;
                         fi
                         ;;
      sync)              runstr="sync_env"
                         if [ ! -z "$2" ] && [[ ! $2 == -* ]]; then
                           ENV="$2" ; shift ;
                         fi
                         ;;
      *)                 echo "invalid option: $1" ; display_help 1 ;;
    esac
    shift
  done

  # directories...
  MAGE_ROOT="$REPO_ROOT/$WEBROOT"

  $runstr
fi
