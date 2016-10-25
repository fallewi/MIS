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

RETURN_VAL=""

for i in $FILE_LIST
do

    OUTPUT=$($ASSET_DIR/vendor/bin/mgf $i 2> /dev/null)
    if [ "$OUTPUT" != "" ] && [ "$OUTPUT" != "\n" ]; then
        RETURN_VAL=$RETURN_VAL"\"$OUTPUT\""
    fi

done


if [ "$RETURN_VAL" != "" ] && [ "$RETURN_VAL" != "\n" ]; then
   echo "$RETURN_VAL"
   exit 1
fi

exit 0
