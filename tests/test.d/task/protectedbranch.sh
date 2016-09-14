#!/usr/bin/env bash

#############################
# Put testvar overrides here
#############################

# CHECK_DIRS=( \
#     app/code/community/Aoe \
# )
# CHECK_FILE_EXTS=( \
#     xml \
# )

source setup.sh

if [ -z "$BASE_BRANCH" ]; then
    exit 0
fi

if [ -z "$FILE_LIST" ]; then

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

