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

FAILURE_FLAG=0

for i in $FILE_LIST
do
    OUTPUT=$(php -l $i)

    if [ $? -ne 0 ]; then
        FAILURE_FLAG=1
    fi

    echo $OUTPUT
done

exit $FAILURE_FLAG

