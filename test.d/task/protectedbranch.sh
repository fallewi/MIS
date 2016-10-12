#!/usr/bin/env bash

#################################################
# Put envar overrides here, comma-seperated list
#################################################

#CHECK_DIRS=(app/code/local/BlueAcorn/Foo,app/code/local/BlueAcorn/Bar)
#BLACKLIST_DIRS=(app/code/local/BlueAcorn/Foo/Model)
#CHECK_FILE_EXTS=(php$,phtml$,css$)    <--- regex syntax
#DIFF_BRANCH="origin/master"

if [ -z "$BASE_BRANCH" ]; then
    exit 0
fi

PROTECTED_BRANCHES="develop origin/develop master origin/master"

for i in $PROTECTED_BRANCHES; do
    if [ "$i" = "$BASE_BRANCH" ]; then
        echo "Warning: Issuing Pull Request into protected branch '$i'"
        exit 1
    fi
done

exit 0

