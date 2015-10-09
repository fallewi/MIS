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
    
    vars [environment]   Prints variables from current [or passed] environment
                         (from envars files)

    make <environment>   Generate a new environment from templates


  Options:
    --help               Display help
                         
    --quiet | -q         semantic output

    --force | -f         Overwrite files when setting an environment

    --skip-ansible       Skip ansible templates when generating an environment

    --skip-env           Skip environment folders when generating an environment

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

# defaults
QUIET=${INIT_QUIET:-false}
WEBROOT=${INIT_WEBROOT:-"webroot"}
FORCE_OVERWRITE=${INIT_FORCE_OVERWRITE:-false}
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


  [ -d "$REPO_ROOT/env/$ENV" ] || error "environment $ENV not found"
    
  run_task "env_conf" "linking config files"
  run_task "env_mark" "marking environment as '$ENV'"
  [ "$ENV" = "local" ] && run_task "env_local" "extra `local` environment setup"
  
  printf "\n [+] configured environment: \033[35m %s \n\033[0m\n\n" "$ENV"

}

print_env(){
  if ! $QUIET; then printf "Current environment: "; fi
  [ -e $REPO_ROOT/env/current ] || error "uninitialized"
  type greadlink >/dev/null 2>&1 && READLINK="greadlink" || READLINK="readlink" 
  echo $($READLINK "$REPO_ROOT/env/current")
}

print_list(){
  if ! $QUIET; then printf "Available environments: "; fi
  cd $REPO_ROOT/env/
  find . -maxdepth 1 -not -path "." -type d | sed 's/.\///'
}

print_vars(){

  ENV=${ENV:-current}

  [ -e $REPO_ROOT/env/envars ] || error "repo appears uninitialized"
  [ -e $REPO_ROOT/env/$ENV ] || error "environment not found"
  [ -e $REPO_ROOT/env/$ENV/envars ] || error "no envars for $ENV environment"

  ! $QUIET && printf "\n\033[35m%s\033[0m vars -->\n" "$ENV"
  
  local CMD="set -a ; \
    source $REPO_ROOT/env/envars ; \
    source $REPO_ROOT/env/$ENV/envars ; \
    printenv | sed '/^\(SHLVL\|PWD\|_\)=/d'"
  env -i sh -c "$CMD"  
}


# environment fns
#################

env_conf() {
  link() {
    SRC=$REPO_ROOT/env/$ENV/$1
    DEST=$REPO_ROOT/$1
  
    if [ ! -e $SRC ]; then
      echo "skipping $1"; return
    fi
  
    if ! $FORCE_OVERWRITE && [ -e $DEST ]; then
      prompt_confirm "Overwrite $1?" || return
    fi
  
    echo "linking $1"
    if [ -e $DEST ]; then 
     rm $DEST 
    elif [ ! -d $(dirname $DEST) ]; then
      mkdir -p $(dirname $DEST)
    fi
    ln -s $SRC $DEST 
  }

  link "$WEBROOT/.htaccess"
  link "$WEBROOT/robots.txt"
  link "$WEBROOT/app/etc/local.xml"
}


env_mark(){
  cd $REPO_ROOT/env || error "could not change to environment directory"
  [ -L "$REPO_ROOT/env/current" ] && rm current 
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

  [ -e $REPO_ROOT/env/envars ] || error "repo appears uninitialized"
  source $REPO_ROOT/env/envars
  
  source $BLUEACORN_DIR/lib/functions || error \
    "could not source BA functions" "run bootstrap or restart your shell"

  [ -z "$ENV" ] && read -p "environment name to generate: " ENV

  local ENV_DIR="$REPO_ROOT/env/$ENV"

  local SED_REPLACE="s|@ENV|$ENV|g;s|@CLIENT_CODE|$CLIENT_CODE|g;s|@REPO_REMOTE|$REPO_REMOTE|g;s|@DOMAIN|$DOMAIN|g"
  
  [ -d $SKEL_DIR ] || error "$SKEL is not a skel environment"
  
  if ! $SKIP_ENV ; then
  
    [ -d "$ENV_DIR" ] && error "environment \"$ENV\" already exists"
    
    echo "skel environments -->"
    cd $REPO_ROOT/.skel/env
    find . -maxdepth 1 -not -path "." -type d | sed 's/.\///' | grep -v local
   
    printf "\nskel environment to base \033[35m%s\033[0m on: " "$ENV" 
    read -r SKEL
    echo
  
    local SKEL_DIR="$REPO_ROOT/.skel/env/$SKEL"
  
    echo "  generating from $SKEL_DIR ..."
    cp -a $SKEL_DIR $REPO_ROOT/env/$ENV
  
    echo "  activating $ENV ..."
    cd $REPO_ROOT/env/$ENV
    for file in `find * -type f`; do
      sed_inplace "$file" "$SED_REPLACE"
    done
  fi
  
  if ! $SKIP_ANSIBLE ; then 
    [ -d "$ENV_DIR" ] || error "environment \"$ENV\" does not exist"
    
    echo "  generating ansible templates..."
    cd $REPO_ROOT/ansible/.skel || error "ansible skel dir missing"
    for file in *; do
      cp $file $REPO_ROOT/ansible/${ENV}.$file
      sed_inplace "$REPO_ROOT/ansible/${ENV}.$file" "$SED_REPLACE"
    done
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
      *)                 echo "invalid option: $1" ; display_help 1 ;;                    
    esac
    shift
  done

  # directories...
  MAGE_ROOT="$REPO_ROOT/$WEBROOT"
  
  $runstr
fi
