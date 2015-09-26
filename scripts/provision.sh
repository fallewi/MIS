#!/usr/bin/env bash

# globals
#########

type greadlink >/dev/null 2>&1 && CWD="$(dirname "$(greadlink -f "$0")")" || \
  CWD="$(dirname "$(readlink -f "$0")")"
REPO_ROOT=`git rev-parse --show-toplevel`


CMD_HELP_PREFIX="A utility for provisioning hosts in an environment.

  Configures httpd, php, nfs, etc. 

  Usage: provision.sh <environment>
" 

ANSIBLE_PLAYBOOK="provision.yml"


# runtime
#########

. $CWD/.functions.sh || error "unable to load shared functions"
ansible_command $@

exit $?