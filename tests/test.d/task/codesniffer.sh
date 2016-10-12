#!/usr/bin/env bash

#################################################
# Put envar overrides here, comma-seperated list
#################################################

#CHECK_DIRS=(app/code/local/BlueAcorn/Foo,app/code/local/BlueAcorn/Bar)
#BLACKLIST_DIRS=(app/code/local/BlueAcorn/Foo/Model)
#CHECK_FILE_EXTS=(php$,phtml$,css$)    <--- regex syntax
#DIFF_BRANCH="origin/master"


source setup.sh

[ ! -e $ASSET_DIR/phpcs.phar ] && curl -L https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar -o $ASSET_DIR/phpcs.phar

chmod +x $ASSET_DIR/phpcs.phar

if [ -z "$FILE_LIST" ]; then
    exit 0
fi

RULESETS_DIR="${TEST_DIR}/asset/rulesets"

$ASSET_DIR/phpcs.phar --error-severity=1 --warning-severity=99 --extensions=php --standard=$RULESETS_DIR/codesniffer/ruleset.xml $FILE_LIST

exit $?
