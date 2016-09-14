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

[ ! -e $ASSET_DIR/phpcs.phar ] && curl -L https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar -o $ASSET_DIR/phpcs.phar

chmod +x $ASSET_DIR/phpcs.phar

if [ -z "$FILE_LIST" ]; then
    exit 0
fi

RULESETS_DIR="${TEST_DIR}/asset/rulesets"

$ASSET_DIR/phpcs.phar --extensions=php --standard=$RULESETS_DIR/codesniffer/ruleset.xml $FILE_LIST

exit $?
