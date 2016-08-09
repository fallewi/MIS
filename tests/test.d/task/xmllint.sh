#!/usr/bin/env bash

#############################
# Put testvar overrides here
#############################

# CHECK_DIRS=( \
#     app/code/community/Aoe \
# )
 CHECK_FILE_EXTS=( \
     xml \
 )

source setup.sh

if [ -z "$FILE_LIST" ]; then
    exit 0
fi

xmllint $FILE_LIST

exit $?

