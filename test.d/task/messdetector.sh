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

curl -L http://static.phpmd.org/php/latest/phpmd.phar -o $ASSET_DIR/phpmd.phar
chmod +x $ASSET_DIR/phpmd.phar


if [ -z "$FILE_LIST" ]; then
    exit 0
fi

RULESETS_DIR="${TEST_DIR}/asset/rulesets"
FAILURE_FLAG=0

for i in $FILE_LIST
do

    $ASSET_DIR/phpmd.phar $i text $RULESETS_DIR/messdetector/ruleset.xml

    # Since phpmd must be run on each file individually, we can't use a single check for failure. Need a running check.
    if [ $? -ne 0 ]; then
        FAILURE_FLAG=1
    fi
done

exit $FAILURE_FLAG
