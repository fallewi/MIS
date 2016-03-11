#!/usr/bin/env bash

source setup.sh

command -v phpcs >/dev/null 2>&1 || \
{ echo "The command phpcs (PHP Code Sniffer) is required but not installed. Aborting." >&2; exit 1; }

RULESETS_DIR="${TEST_DIR}/assets/rulesets"

cd $REPO_ROOT

phpcs --extensions=php --standard=$RULESETS_DIR/codesniffer.xml $FILE_LIST

exit $?
