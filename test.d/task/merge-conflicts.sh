#!/usr/bin/env bash

#################################################
# Put envar overrides here, comma-seperated list
#################################################

#CHECK_DIRS=(app/code/local/BlueAcorn/Foo,app/code/local/BlueAcorn/Bar)
#BLACKLIST_DIRS=(app/code/local/BlueAcorn/Foo/Model)
#CHECK_FILE_EXTS=(php$,phtml$,css$)    <--- regex syntax
#DIFF_BRANCH="origin/master"

source setup.sh

if [ -z "$FILE_LIST" ]; then
    exit 0
fi

OUT=$(grep -rlE "<<<<<<<|>>>>>>>" $FILE_LIST)

if [ $? -ne 1 ] ; then
   echo "Possible merge conflicts detected in: "$OUT
   exit 1
fi;