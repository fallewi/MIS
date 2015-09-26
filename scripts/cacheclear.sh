#!/usr/bin/env bash

# globals
#########

type greadlink >/dev/null 2>&1 && CWD="$(dirname "$(greadlink -f "$0")")" || \
  CWD="$(dirname "$(readlink -f "$0")")"
REPO_ROOT=`git rev-parse --show-toplevel`


CMD_HELP_PREFIX="A utility for clearing the cache of an active environment. 
  
  Typically clears object and FPC caches - see the ansible playbooks for details

  Usage: cacheclear.sh <environment>
" 

ANSIBLE_PLAYBOOK="cacheclear.yml"


# runtime
#########

. $CWD/.functions.sh || error "unable to load shared functions"
ansible_command $@

exit $?