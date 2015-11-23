#!/usr/bin/env bash

# utility
#########

display_help() {
  cat <<-EOF

  A utility for initializing client site codebases

  Usage: manage.sh <command> [options]

  Commands:
    init                 Initialize a site for first time
                         (clones and executes green-pistachio)

    downstream-skel      Fetch the latest downstream-skel.sh for downstreaming
                         from git@github.com:BlueAcornInc/magento-skel.git

    localize             Runs string replacements on templatized skel files

  Options:
    --help               Display help

    --force | -f         Overwrite files during init and environment setting

    --webroot <webroot>  Directory holding the magento distribution
                         (def: webroot)

    --quiet | -q         semantic output

    --client-code        Client Code (e.g. SIG)

    --repo-remote        Repository (e.g. git@github.com:BlueAcornInc/SIG.git)

    --domain             Top Level Domain (e.g. signaturehardware.com)

    --gp-remote <remote> Repository of blueacornui / green-pistachio
                         (def: git@github.com:BlueAcornInc/green-pistachio.git)

    --gp-skip            Skip installing blueacornui / green-pistachio


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

. $BLUEACORN_DIR/lib/functions || error "missing BA funcs" "run bootstrap?"

# globals
#########

type greadlink >/dev/null 2>&1 && CWD="$(dirname "$(greadlink -f "$0")")" || \
  CWD="$(dirname "$(readlink -f "$0")")"
REPO_ROOT=`git rev-parse --show-toplevel`

# defaults
QUIET=${INIT_QUIET:-false}
WEBROOT=${INIT_WEBROOT:-"webroot"}
FORCE_OVERWRITE=${INIT_FORCE_OVERWRITE:-false}
GP_REMOTE=${INIT_GP_REMOTE:-"git@github.com:BlueAcornInc/green-pistachio.git"}
SKIP_GP=false


# run functions
###############

run_task() {
  echo
  echo "  * starting: $2 -->"
  echo

  $1
}

repo_init(){
  run_task "init_localize" "localize boilerplate"
  run_task "init_deploykey" "generate deployment key"
  run_task "init_blueacornui" "modman init + green-pistachio/blueacornui"
}

repo_localize(){
  run_task "init_localize" "localize boilerplate"
}


# initialization fns
####################


init_localize(){

  # attempt to remember initialized values
  [ -f "$REPO_ROOT/skel/env/envars" ] && source $REPO_ROOT/skel/env/envars

  if [ -z "$CLIENT_CODE" ] || [ "$CLIENT_CODE" = "@CLIENT_CODE" ] ; then
    read -p "Value to use for CLIENT_CODE (e.g. SIG): " CLIENT_CODE
    echo
  fi


  if [ -z "$REPO_REMOTE" ] || [ "$REPO_REMOTE" = "@REPO_REMOTE" ] ; then
    read -p "Value to use for REPO_REMOTE (e.g. git@github.com:BlueAcornInc/SIG.git): " REPO_REMOTE
    echo
  fi

  if [ -z "$DOMAIN" ] || [ "$DOMAIN" = "@DOMAIN" ] ; then
    read -p "Value to use for Base Domain (e.g. signaturehardware.com - NOT www.signautehardware.com): " DOMAIN
    echo
  fi

  local SED_REPLACE="s|@CLIENT_CODE|$CLIENT_CODE|g;s|@REPO_REMOTE|$REPO_REMOTE|g;s|@DOMAIN|$DOMAIN|g"

  local FILES="
    README.md
    skel/env/envars
    docs/
  "

  for file in $FILES; do
    cd $REPO_ROOT

    if [ -d $file ]; then
      cd $file
      for file in `find * -type f`; do
        echo "templating $file"
        sed_inplace $file $SED_REPLACE
      done
    else
      echo "templating $file"
      sed_inplace $file $SED_REPLACE
    fi
  done


  #cd $REPO_ROOT/.skel
  #
  # for file in `find * -type f`; do
  #   if $FORCE_OVERWRITE || [ ! -e "$REPO_ROOT/$file" ]; then
  #     echo "making $file ..."
	#     # because Darwin, no `cp --parents $file $REPO_ROOT` for you!
	#     local target_dir="$REPO_ROOT/$(dirname $file)"
	#     local target_file="$target_dir/$(basename $file)"
	#     [ -d $target_dir ] || mkdir -p $target_dir
  #
	#     cat $file | sed "$SED_REPLACE" > $target_file  || \
	#       error "error writing $target_file"
  #   fi
  # done
}

init_deploykey(){

  local KEYFILE="$REPO_ROOT/skel/ansible/files/github-deploy.key"

  [ -f "$KEYFILE" ] && return

  [ -z "$CLIENT_CODE" ] && read -p "Value to use for CLIENT_CODE (e.g. SIG): " CLIENT_CODE
  echo

  echo "generating deployment key..."
  ssh-keygen -t rsa -b 4096 -C "deploy-key@${CLIENT_CODE}-$(date -I)" -N "" \
    -f $KEYFILE

  echo
  echo "!!! deployment key generated."
  echo "    please add $KEYFILE.pub"
  echo "    as a deploy key to the github repository !!!"
  echo

}



init_blueacornui(){

  if $SKIP_GP ; then
    echo "skipping..."
    return
  fi

  type modman >/dev/null 2>&1 || error "modman is not installed"

  echo "checking for modman..."
  if [ ! -d "$MAGE_ROOT/.modman" ]; then
    cd $MAGE_ROOT
    modman init
  fi

  echo "checking for blueacornui..."
  if [ ! -d "$REPO_ROOT/blueacornui" ]; then
    cd $MAGE_ROOT
    modman clone --copy $GP_REMOTE
  fi

}


# utility fns
#############

pilot_update(){
  # git@github.com:BlueAcornInc/magento-skel.git

  curl https://get.blueacorn.net/downstream-skel.sh -o \
    $REPO_ROOT/downstream-skel.sh && chmod +x $REPO_ROOT/downstream-skel.sh

  echo
  echo "begin the downstreaming process by executing ./downstream-skel.sh ..."
  echo

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
      --gp-remote)       GP_REMOTE="$2" ; shift ;;
      --gp-skip)         SKIP_GP=true ;;
      --client-code)     CLIENT_CODE="$2" ; shift ;;
      --repo-remote)     REPO_REMOTE="$2" ; shift ;;
      --domain)          DOMAIN="$2" ; shift ;;
      --webroot)         WEBROOT="$2" ; shift ;;
      init)              runstr="repo_init" ;;
      downstream-skel)   runstr="pilot_update" ;;
      localize)          runstr="repo_localize" ;;
      *)                 echo "invalid option: $1" ; display_help 1 ;;
    esac
    shift
  done

  # directories...
  MAGE_ROOT="$REPO_ROOT/$WEBROOT"
  CONF_DIR="$REPO_ROOT/env/$ENV"

  $runstr
fi
