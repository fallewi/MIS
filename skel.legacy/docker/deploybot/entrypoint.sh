#!/bin/sh
DEPLOY_ENV=${DEPLOY_ENV:-$2}
DEPLOY_REF=${DEPLOY_REF:-$3}

display_help() {
  cat <<-EOF

  Usage: entrypoint.sh deploy [<environment>] [deploy_ref]

   docker run deploybot:CLIENT_CODE deploy develop

   e.g.: entrypoint.sh deploy develop
         entrypoint.sh deploy production 8ca9c47

  Also accepts DEPLOY_ENV and DEPLOY_REF envars
    e.g.: export DEPLOY_ENV="qa"; \
          export DEPLOY_REF="master"; \
          entrypoint.sh deploy

EOF
}

error(){
  printf "\033[31m%s\n\033[0m" "$@" >&2
  exit 1
}

[ -z $1 ] && display_help && error "no command specified"

if [ "$1" = "deploy" ]; then
  [ -z $DEPLOY_ENV ] && display_help && error "deploy environment not specified"

  echo "deploying $DEPLOY_ENV ref $DEPLOY_REF"

  #
  # use reference repositories to speed up checkouts
  #   http://chimera.labs.oreilly.com/books/1230000000561/ch06.html#refrep
  #
  # reference repositories [generally stored in /deploybot/references/]
  #   should be bind-mounted on the deploybot host to always avoid cloning repo
  #

  # @TODO, every so ofte/n update the reference repository
  if [ ! -d $REFERENCE_REPO/refs ]; then
    git clone --mirror $REFERENCE_REPO_REMOTE $REFERENCE_REPO || error \
      "error cloning reference repository"
  fi

  # clone deployment repository to /tmp/checkout
  git clone --reference $REFERENCE_REPO $REFERENCE_REPO_REMOTE /tmp/checkout
  cd /tmp/checkout

  # determine default DEPLOY_REF
  #  runs the skel bin/env var command to determine REPO_REF value
  if [ -z "$DEPLOY_REF" ]; then
    DEPLOY_REF="$(bin/env var REPO_REF $DEPLOY_ENV)" || error \
      "error determining default DEPLOY_REF"
  fi

  # fetch and checkout deployment ref
  git fetch origin $DEPLOY_REF || error \
    "error fetching origin/$DEPLOY_REF" "ensure it exists"
  git checkout $DEPLOY_REF || error \
    "error checking out $DEPLOY_REF"

  # execute deployment (uses /root/.ssh/id_rsa as key for connecting to servers)
  #  this is typically bind-mounted on the deploybot host
  bin/deploy -y $DEPLOY_ENV --extra-vars REPO_REF="$DEPLOY_REF" || error \
    "error deploying"

  exit $?
fi

echo "deploy command not issued. executing $@"
exec "$@"
