#!/usr/bin/env bash

# utility
#########

error(){
  printf "\033[31m%s\n\033[0m" "$@" >&2
  exit 1
}


# globals
#########

__cwd=$( cd $(dirname $0) ; pwd -P )

# read envars
source $__cwd/../../env/appvars || error "could not read appvars"

# copy deploy key (used for git checkouts)
cp $__cwd/../../deploy.key $__cwd/deploy.key || error "could not copy deploy.key" \
  "have you initialized the skel?"

[ -z "$CLIENT_CODE" ] && error \
  "CLIENT_CODE must be set."

[ -z "$REPO_REMOTE" ] && error \
  "REPO_REMOTE must be set."

[ ${CLIENT_CODE:0:1} = "@" ] && error \
    "placeholder value detected for CLIENT_CODE" "please set appropriately"

[ ${REPO_REMOTE:0:1} = "@" ] && error \
    "placeholder value detected for REPO_REMOTE" "please set appropriately"

DOCKERFILE="Dockerfile"

# normalize client code to docker tag [removes non alpha chars]
IMAGE_TAG=${CLIENT_CODE//[^a-zA-Z]/}
IMAGE_NAME="deploybot:$IMAGE_TAG"

echo
echo "========================================"
echo "building $IMAGE_NAME..."
echo "========================================"
echo

cd $__cwd

if [ ! -e Dockerfile ]; then
  [ -e Dockerfile.skel ] || error "no Dockerfile or Dockerfile.skel found"
  DOCKERFILE="Dockerfile.skel"
fi



docker build \
  --build-arg CLIENT_CODE="$CLIENT_CODE" \
  --build-arg REPO_REMOTE="$REPO_REMOTE" \
  -t deploybot:$CLIENT_CODE -f $DOCKERFILE . || error \
    "failed to build $IMAGE_NAME from $DOCKERFILE"

echo "built $IMAGE_NAME, image ID: $(docker images -q $IMAGE_NAME)"
