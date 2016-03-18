#!/usr/bin/env bash

curl -OL http://static.phpmd.org/php/latest/phpmd.phar
chmod +x phpmd.phar

source setup.sh


RULESETS_DIR="${TEST_DIR}/assets/rulesets"
FAILURE_FLAG=0

for i in $FILE_LIST
do

    ./phpmd.phar $i text $RULESETS_DIR/messdetector.xml

    # Since phpmd must be run on each file individually, we can't use a single check for failure. Need a running check.
    if [ $? -ne 0 ]; then
        FAILURE_FLAG=1
    fi
done

exit $FAILURE_FLAG
