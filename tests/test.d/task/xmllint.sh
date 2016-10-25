#!/usr/bin/env bash

#################################################
# Put envar overrides here, comma-seperated list
#################################################

#CHECK_DIRS=(app/code/local/BlueAcorn/Foo,app/code/local/BlueAcorn/Bar)
#BLACKLIST_DIRS=(app/code/local/BlueAcorn/Foo/Model)
#CHECK_FILE_EXTS=(php$,phtml$,css$)    <--- regex syntax
#DIFF_BRANCH="origin/master"

CHECK_FILE_EXTS=(xml$)

source setup.sh

if [ -z "$FILE_LIST" ]; then
    exit 0
fi

xmllint $FILE_LIST

exit $?

