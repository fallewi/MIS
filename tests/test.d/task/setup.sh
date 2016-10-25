#!/usr/bin/env bash

# This file should be used for variables that will be accessed across multiple tests
# All tests should source this file to include the vars

# variable setup
#################

# envars setup
IFS=", "$'\n'
read -a CHECK_DIRS <<<$CHECK_DIRS
read -a BLACKLIST_DIRS <<<$BLACKLIST_DIRS
read -a CHECK_FILE_EXTS <<<$CHECK_FILE_EXTS

if [ -z "$COMMIT_HASH" ]; then
  HEAD_BRANCH="HEAD"
else
  HEAD_BRANCH="$COMMIT_HASH"
fi

# Directory setup
TEST_DIR="$( cd ../"$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
REPO_ROOT=$(git rev-parse --show-toplevel)
APP_ROOT="${REPO_ROOT}/webroot"
ASSET_DIR=$TEST_DIR/asset

# functions
############


filter_array() {

  _OUTPUT=""
  _NEEDLE=$1;
  shift;
  local _HAYSTACK=( $(echo $@) );

  for i in "${_HAYSTACK[@]}"; do
    if [ ${#i} -ge 5 ]; then
      _OUTPUT=$_OUTPUT$(grep -E$_INVERSE "$_NEEDLE" <<< "$i" 2>&1)" "
    fi;
  done;

  echo $_OUTPUT
}


filter_array_multi() {

  _INVERSE=""
  if [ "$1" = "-v" ]; then
    _INVERSE="v";
    shift;
  fi;

  local IFS=",";
  local -a _HAYSTACK=("${!1}");
  local -a _NEEDLES=("${!2}");

  if [[ "${_NEEDLES[@]}" != "" ]]; then
    _TMP_FILE_LIST=""
    for i in $_NEEDLES; do
      _TMP_FILE_LIST=$_TMP_FILE_LIST$(filter_array "$i" ${_HAYSTACK[@]} 2>&1)
    done;
    FILE_LIST=$_TMP_FILE_LIST
  fi
}

format_file_list() {
  read -a FILE_LIST <<<$FILE_LIST
  FILE_LIST=( "${FILE_LIST[@]/#/$REPO_ROOT/}" )
  FILE_LIST="${FILE_LIST[@]}"
}

format_untracked_files() {

  for i in $(git ls-files $REPO_ROOT --exclude-standard --others | grep "webroot"); do
    _FILE=${i#"${i%%[a-z]*}"}
    FILE_LIST=$FILE_LIST" "$_FILE
  done;

}

# runtime
##########

# get list of all changed files
FILE_LIST=$(git diff --name-only $HEAD_BRANCH..$DIFF_BRANCH)
format_untracked_files

# filter out non-whitelist directories
filter_array_multi FILE_LIST[@] CHECK_DIRS[@]

# filter out non-whitelist file extensions
filter_array_multi FILE_LIST[@] CHECK_FILE_EXTS[@]

# filter out blacklist
filter_array_multi -v FILE_LIST[@] BLACKLIST_DIRS[@]

# format FILE_LIST to use REPO_ROOT
format_file_list
