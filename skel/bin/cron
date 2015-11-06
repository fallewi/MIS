#!/usr/bin/env bash

# globals
#########

type greadlink >/dev/null 2>&1 && CWD="$(dirname "$(greadlink -f "$0")")" || \
  CWD="$(dirname "$(readlink -f "$0")")"
REPO_ROOT=`git rev-parse --show-toplevel`


CMD_HELP_PREFIX="A utility for scheduling tasks in an environment.

  See the environment playbook for a list of tasks that get scheduled

  Usage: cron.sh <environment>
" 

ANSIBLE_PLAYBOOK="cron.yml"


# runtime
#########

. $CWD/.functions.sh || error "unable to load shared functions"
ansible_command $@

exit $?