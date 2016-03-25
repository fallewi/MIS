#!/usr/bin/env bash

source setup.sh

FAILURE_FLAG=0

for i in $FILE_LIST
do
    OUTPUT=$(php -l $i)

    if [ $? -ne 0 ]; then
        FAILURE_FLAG=1
    fi

    echo $OUTPUT
done

exit $FAILURE_FLAG

