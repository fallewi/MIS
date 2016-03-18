#!/usr/bin/env bash

curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
chmod +x phpcs.phar


source setup.sh

RULESETS_DIR="${TEST_DIR}/assets/rulesets"

./phpcs.phar --extensions=php --standard=$RULESETS_DIR/codesniffer.xml $FILE_LIST
exit $?
