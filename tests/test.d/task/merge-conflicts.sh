#!/usr/bin/env bash

#############################
# Put testvar overrides here
#############################

# CHECK_DIRS=( \
#     app/code/community/Aoe \
# )
# CHECK_FILE_EXTS=( \
#     php \
# )

source setup.sh

if [ -z "$FILE_LIST" ]; then
    exit 0
fi

OUT=$(grep -rlE "<<<<<<<|>>>>>>>" $FILE_LIST)

if [ $? -ne 1 ] ; then
   echo "Possible merge conflicts detected in: "$OUT
   exit 1
fi;