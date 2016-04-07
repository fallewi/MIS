#!/usr/bin/env bash

curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
chmod +x phpcs.phar


source setup.sh

if [ -z "$FILE_LIST" ]; then
    exit 0
fi

RULESETS_DIR="${TEST_DIR}/assets/rulesets"

./phpcs.phar --extensions=php --standard=$RULESETS_DIR/codesniffer/ruleset.xml $FILE_LIST
exit $?
