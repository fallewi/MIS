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
