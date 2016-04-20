#!/usr/bin/env bash

# utility
#########


display_help() {
  cat <<-EOF

  A utility to start and stop QA environments. Use this vs native docker-compose

  Requires docker-machine, and access to the following nodes
    qa-1 : node-a
    qa-2 : node-b
    qa-3 : node-z

  Usage: manage.sh <environment> <arg>
    ./manage.sh qa-1 up

  Arguments:
    up                   start a qa environment

    down                 stop a qa environment

  Options:

    -v | --volumes       Creates and or Deletes volumes when starting/stopping
    -l | --local         Runs containers locally vs setting the docker-machine

    --help               Display help

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

type greadlink >/dev/null 2>&1 && CWD="$(dirname "$(greadlink -f "$0")")" || \
  CWD="$(dirname "$(readlink -f "$0")")"


SKIP_VOLUMES=true
SKIP_MACHINE=false
COMPOSE_FILE="docker-compose.skel.yml"


# runtime
#########

bootstrap(){

  cd $CWD

  [ -z $ENV ] && error "please specify a qa environment, e.g." "  ./build.sh qa-1"
  [ -e ../../env/$ENV/envars ] || error "missing ../../env/$ENV/envars" "is $ENV a valid environment?"

  # export vars
  export ENV=$ENV
  set -a
  . ../../env/appvars || error "unable to source appvars"
  . ../../env/$ENV/envars || error "unable to source envars"
  set +a


  [ -z "$CLIENT_CODE" ] && error \
    "CLIENT_CODE must be set."

  [ -z "$REPO_REMOTE" ] && error \
    "REPO_REMOTE must be set."

  [ -z "$SNAPSHOT_BASE_ENV" ] && error \
    "SNAPSHOT_BASE_ENV must be set. Typically via env/$ENV/envars." "is this a qa environment?"

  [ ${CLIENT_CODE:0:1} = "@" ] && error \
      "placeholder value detected for CLIENT_CODE" "has skel been initialized?"

  [ ${REPO_REMOTE:0:1} = "@" ] && error \
      "placeholder value detected for REPO_REMOTE" "has skel been initialized?"


  [ -e docker-compose.yml ] && COMPOSE_FILE="docker-compose.yml"
  COMPOSE_FLAGS="-f $COMPOSE_FILE -p ${ENV}-${CLIENT_CODE}"

  $SKIP_MACHINE && return

  case $ENV in
    qa-1) configure_docker_machine node-a ;;
    qa-2) configure_docker_machine node-b ;;
    qa-3) configure_docker_machine node-z ;;
    *)    error "no docker-machine has been configured for $ENV" ;;
  esac
}

configure_docker_machine(){
  local SHELL=${SHELL:-"sh"}
  eval $(docker-machine env $1 --shell $SHELL)
  [ $(docker-machine active) = "$1" ] || error "unable to configure $1 machine"
  docker info || error "unable to communicate with $1 machine"
}

env_start(){

  $SKIP_VOLUMES || make_volumes

  echo
  echo "========================================"
  echo "building $ENV environment containers..."
  echo "========================================"
  echo

  # copy deploy key (used for git checkouts)
  cp ../../deploy.key $CWD/deploy.key || error "could not copy deploy.key" \
    "have you initialized the skel?"

  # alpine variants
  docker pull registry.badevops.com/mage-term:m1
  docker pull registry.badevops.com/mage-phpfpm:m1
  docker pull registry.badevops.com/mage-nginx:m1

  # debian variants
  docker pull registry.badevops.com/mage-term:m1-debian
  docker pull registry.badevops.com/mage-phpfpm:m1-debian
  docker pull registry.badevops.com/mage-nginx:m1-debian

  docker-compose $COMPOSE_FLAGS stop
  docker-compose $COMPOSE_FLAGS pull
  docker-compose $COMPOSE_FLAGS build

  echo
  echo "========================================"
  echo "starting $ENV environment containers..."
  echo "========================================"
  echo

  docker-compose $COMPOSE_FLAGS up -d
}


env_stop(){

  echo $COMPOSE_FLAGS

  if $SKIP_VOLUMES; then
    echo
    echo "========================================"
    echo "stopping $ENV environment containers..."
    echo "========================================"
    echo
    docker-compose $COMPOSE_FLAGS stop

  else
    echo
    echo "========================================"
    echo "removing $ENV containers and volumes..."
    echo "========================================"
    echo
    docker-compose $COMPOSE_FLAGS down -v
  fi
}



make_volumes(){
  echo
  echo "========================================"
  echo "creating $ENV volumes  containers..."
  echo "========================================"
  echo
  #@TODO? named volume making
  echo "... not yet supported."
}

if [ $# -eq 0 ]; then
  display_help 1
else
  while [ $# -ne 0 ]; do
    case $1 in
      -h|--help|help)    display_help ;;
      -v|--volumes)      SKIP_VOLUMES=false ;;
      -l|--local)        SKIP_MACHINE=true ;;
      qa*)               ENV=$1 ;;
      up)                runstr="env_start" ;;
      down)              runstr="env_stop" ;;
      *)                 echo "invalid option: $1" ; display_help 1 ;;
    esac
    shift
  done

  [ -z $runstr ] && echo "invalid option: $1" && display_help 1

  bootstrap
  $runstr
  exit $?
fi
