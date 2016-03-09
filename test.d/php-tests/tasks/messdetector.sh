#!/usr/bin/env bash

source setup.sh

command -v phpmd >/dev/null 2>&1 || \
{ echo "The command phpmd (PHP Mess Detector) is required but not installed.  Aborting." >&2; exit 1; }

RULESETS_DIR="${TEST_DIR}/assets/rulesets"
FAILURE_FLAG=0

for i in $FILE_LIST
do
    phpmd $REPO_ROOT/$i text $RULESETS_DIR/messdetector.xml

    # Since phpmd must be run on each file individually, we can't use a single check for failure. Need a running check.
    if [ $? -ne 0 ]; then
        FAILURE_FLAG=1
    fi
done

exit $FAILURE_FLAG
