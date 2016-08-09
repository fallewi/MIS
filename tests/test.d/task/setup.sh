#!/usr/bin/env bash

# This file should be used for variables that will be accessed across multiple tests
# All tests should source this file to include the vars

source testvars

# Directory setup
TEST_DIR="$( cd ../"$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
REPO_ROOT=`git rev-parse --show-toplevel`
APP_ROOT="${REPO_ROOT}/webroot"
ASSET_DIR=$TEST_DIR/asset

# Get the current branch
BRANCH=`git rev-parse --abbrev-ref HEAD`

function join { local IFS="$1"; shift; echo "$*"; }

# Trim down modified files to match testvars specificiations
FILE_LIST=""

for i in $( \
    git diff --name-only  $BRANCH..$BASE_BRANCH \
    | egrep $(join '|' $(printf '%s ' "${CHECK_DIRS[@]}") | tr "\/" "\\/")  \
    | egrep $(join '|' $(printf '%s ' "${CHECK_FILE_EXTS[@]}") | tr "\/" "\\/")$  \
    | if [ ${#BLACKLIST_DIRS} -ne 0 ]; then egrep -v $(join '|' $(printf '%s ' "${BLACKLIST_DIRS[@]}") | tr "\/" "\\/"); else cat; fi  \
    | tr " " "\n" \
    ); do
    FILE_LIST=$FILE_LIST" $REPO_ROOT/"$i
done;