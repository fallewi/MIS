#!/usr/bin/env bash

# globals
#########

type greadlink >/dev/null 2>&1 && CWD="$(dirname "$(greadlink -f "$0")")" || \
  CWD="$(dirname "$(readlink -f "$0")")"
REPO_ROOT=`git rev-parse --show-toplevel`


CMD_HELP_PREFIX="A utility for deploying to an environment.

  Usage: deploy.sh <environment>
" 

ANSIBLE_PLAYBOOK="deploy.yml"


# runtime
#########

. $CWD/.functions.sh || error "unable to load shared functions"
ansible_command $@

exit $?