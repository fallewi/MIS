#!/usr/bin/env bash

#################################################
# Put envar overrides here, comma-seperated list
#################################################

#CHECK_DIRS=(app/code/local/BlueAcorn/Foo,app/code/local/BlueAcorn/Bar)
#BLACKLIST_DIRS=(app/code/local/BlueAcorn/Foo/Model)
#CHECK_FILE_EXTS=(php$,phtml$,css$)    <--- regex syntax
#DIFF_BRANCH="origin/master"

CHECK_DIRS=(app/design/frontend/blueacorn/site/template)
CHECK_FILE_EXTS=(phtml$)

source setup.sh

if [ -z "$FILE_LIST" ]; then
    exit 0
fi

OUT=$(grep "\->query(" $FILE_LIST 2>&1)

if [ $? -eq 0 ]; then
    echo "Warning: possible database queries in templates"
    echo $OUT
    exit 1
fi

exit 0