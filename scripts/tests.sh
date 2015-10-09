#!/bin/sh

# run in docker (requires docker), or locally (requires node/npm, gulp)



#!/usr/bin/env bash

# utility
#########

display_help() {
  cat <<-EOF

  A utility for building and launching client site dockers 

  Usage: launch.sh <command> [options]

  Commands:
    site                 Run this site in a docker

    tests                Run tests in a docker container
                         (tests are located in ../tests)

    ufs                  Run a bash shell with this repository mounted as
                         /ufs/export using a union filesystem (for testing)
    
  Options:
    --help               Display help
    
    --cmd | -c           Pass an alternative command to docker run
    
    --build | -b         Rebuilds the docker image 
                         (useful during Dockerfile updates)
    --no-cache | -nc     Avoid cache during docker build (rebuild completely)
    
    --repo-root-match    docker volume REPO_ROOT match string to replace  
    --repo-root-replace  docker volume REPO_ROOT replace string 


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
REPO_ROOT="$(git rev-parse --show-toplevel)"

[ -e $REPO_ROOT/env/envars ] || error "uninitialized"
source $REPO_ROOT/env/envars

if [ -z "$CLIENT_CODE" ]; then
  error "CLIENT_CODE undefined" "has an environment been set with env.sh?"
fi

FORCE_BUILD=${LAUNCH_FORCE_BUILD:-false}
GIT_REFERENCE=${LAUNCH_GIT_REFERENCE:-false}
DOCKER_RUN_FLAGS=${DOCKER_RUN_FLAGS:-'-t --rm=true'}
DOCKER_BUILD_FLAGS=${DOCKER_BUILD_FLAGS:-'--pull=true'}

UFS_TYPE=$(docker info | grep 'Storage Driver' |sed 's/Storage Driver:[[:blank:]]*\([[:alpha:]]*\)[[:blank:]]*/\1/')
if [ -z "$UFS_TYPE" ]; then
  echo "could not determine UFS_TYPE from docker"
  error "the 'docker' command must be available to this script"
fi


# functions
###########

get_docker_name(){
  DOCKER_NAME=${1,,}
  DOCKER_NAME=${DOCKER_NAME//-/_}
  DOCKER_NAME=${DOCKER_NAME// /_}
  DOCKER_NAME=${DOCKER_NAME//[^a-z0-9_]/}
}

build_docker_image(){

  local DOCKER_IMAGE=$1
  local DOCKER_BUILD_DIR=$2

  if [ ! -d $DOCKER_BUILD_DIR ]; then
    error "$DOCKER_BUILD_DIR does not exist. has environment been set?"
  fi

  # build docker
  if $FORCE_BUILD || [ -z "$(docker images -q $DOCKER_IMAGE)" ]; then
    docker build $DOCKER_BUILD_FLAGS -t $DOCKER_IMAGE $DOCKER_BUILD_DIR 
  fi

}

run_site(){
  local DOCKER_NAME="${CLIENT_CODE,,}_site" 
  local DOCKER_BUILD_DIR="$REPO_ROOT/env/$CONF_ENV/docker"
  #local DOCKER_ID="$(docker ps -aq -f name=$DOCKER_NAME)"
  
  build_docker_image $DOCKER_NAME $DOCKER_BUILD_DIR

  docker run $DOCKER_RUN_FLAGS --name="$DOCKER_NAME" \
    -e "UFS_TYPE=$UFS_TYPE" \
    -e "COMPOSE_PROJECT_NAME=$DOCKER_NAME" \
    -e "DOCKER_REPO_ROOT=$DOCKER_REPO_ROOT" \
    -v /var/run/docker.sock:/var/run/docker.sock \
    $DOCKER_NAME ${DOCKER_COMMAND:-}
}

run_tests_ufs(){
  local DOCKER_NAME="${CLIENT_CODE,,}_tests"
  local DOCKER_BUILD_DIR="$REPO_ROOT/tests"
  #local DOCKER_ID="$(docker ps -aq -f name=$DOCKER_NAME)"
  
  build_docker_image $DOCKER_NAME $DOCKER_BUILD_DIR
  
  docker run $DOCKER_RUN_FLAGS --name="$DOCKER_NAME" \
    -e "UFS_TYPE=$UFS_TYPE" \
    -v ${DOCKER_REPO_ROOT}:/ufs/ro:ro \
    $DOCKER_NAME ${DOCKER_COMMAND:-}
}

run_ufs(){
  local DOCKER_NAME="registry.badevops.com/alpine-ufs"
  
  docker run $DOCKER_RUN_FLAGS \
    -e "UFS_TYPE=$UFS_TYPE" \
    -v ${DOCKER_REPO_ROOT}:/ufs/ro:ro \
    $DOCKER_NAME ${DOCKER_COMMAND:-bash}
}



run_tests(){
  get_docker_name "${CLIENT_CODE}_tests"

  cd $REPO_ROOT/tests
  
  if $FORCE_BUILD || [ -z "$(docker images -q $DOCKER_NAME)" ]; then
    docker build $DOCKER_BUILD_FLAGS -t $DOCKER_NAME . 
  fi
  
  docker run $DOCKER_RUN_FLAGS \
    -v ${DOCKER_REPO_ROOT}:/repo:ro \
    -v ${DOCKER_REPO_ROOT}/tests:/repo/tests:rw \
    -u $UID \
    $DOCKER_NAME ${DOCKER_COMMAND:-}
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
      --build|-b)        FORCE_BUILD=true ;;
      --no-cache|-nc)    DOCKER_BUILD_FLAGS="--no-cache=true" ;;
      --cmd|-c)          DOCKER_RUN_FLAGS="$DOCKER_RUN_FLAGS -i"
                         DOCKER_COMMAND="$2" ;
                         shift ;;
      --repo-root-match) REPO_ROOT_MATCH="$2" ; shift ;;
      --repo-root-replace) REPO_ROOT_REPLACE="$2" ; shift ;;
      site)              runstr="run_site" ;;
      tests)             runstr="run_tests" ;;
      ufs)               DOCKER_RUN_FLAGS="$DOCKER_RUN_FLAGS -i"
                         runstr="run_ufs" ;;
      *)                 echo "invalid option: $1" ; display_help 1 ;;                    
    esac
    shift
  done
  
  
  if [ -z "$DOCKER_REPO_ROOT" ] && [ ! -z "$REPO_ROOT_MATCH" ]; then
    DOCKER_REPO_ROOT=$(echo "$REPO_ROOT" | sed "s|$REPO_ROOT_MATCH|$REPO_ROOT_REPLACE|")
  else
    DOCKER_REPO_ROOT=${DOCKER_REPO_ROOT:-$REPO_ROOT}
  fi
  
  $runstr
  exit $?
fi


