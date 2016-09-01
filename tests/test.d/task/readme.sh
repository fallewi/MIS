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

if [ -z "$FILE_LIST" ]; then
    exit 0
fi

MODULES=""
EXIT_FLAG=0;

# Get module list
for i in $FILE_LIST; do
    # Get the module name from each file. Hardcoded string positions are okay b/c of Magento standard file structure
    MODULES="$MODULES $(echo ${i#$APP_ROOT} | awk -F'/' '{print $4, $5, $6}' | tr " " "/")"
    # Remove duplicate modules names from multiple files in same module
    MODULES=$(echo $MODULES | xargs -n1 | sort -u | xargs)
done

for i in $MODULES; do
    if [ ! -e $APP_ROOT/app/code/$i/doc/README.md ]; then
        echo "Missing README.md for module" $APP_ROOT/app/code/$i". README.md shoud exist at" $( echo $i | awk -F'/' '{print $2, $3}' | tr " " "/")/"doc/"
        EXIT_FLAG=1;
    fi
done;

exit $EXIT_FLAG