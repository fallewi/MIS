#!/bin/bash

################################################################################
# FUNCTIONS
################################################################################



# 3. Create code dump function
createCodeDump() {
    # Content of file archive
    DISTR="
    app
    downloader
    errors
    includes
    js
    lib
    pkginfo
    shell
    skin
    .htaccess
    cron.php
    get.php
    index.php
    install.php
    mage
    *.patch
    *.sh
    var/log/system.log
    var/log/exception.log
    var/log/shipping*.log
    var/log/payment*.log
    var/log/paypal*.log"

    # Create code dump
    DISTRNAMES=
    for ARCHPART in $DISTR; do
        if [ -r "$MAGENTOROOT$ARCHPART" ]; then
            DISTRNAMES="$DISTRNAMES $MAGENTOROOT$ARCHPART"
        fi
    done
    if [ -n "$DISTRNAMES" ]; then
        echo nice -n 15 tar -czhf $CODEFILENAME $DISTRNAMES
        nice -n 15 tar -czhf $CODEFILENAME $DISTRNAMES
    fi
}

# 4. Create DB dump function


################################################################################
# CODE
################################################################################

# Selftest
checkTools

# Magento folder
MAGENTOROOT=./

# Output path
OUTPUTPATH=$MAGENTOROOT

# Input parameters
MODE=
NAME=

OPTS=`getopt -o m:n:o: -l mode:,name:,outputpath: -- "$@"`

if [ $? != 0 ]
then
    exit 1
fi

eval set -- "$OPTS"

while true ; do
    case "$1" in
        -m|--mode) MODE=$2; shift 2;;
        -n|--name) NAME=$2; shift 2;;
        -o|--outputpath) OUTPUTPATH=$2; shift 2;;
        --) shift; break;;
    esac
done

if [ -n "$NAME" ]; then
    CODEFILENAME="$OUTPUTPATH$NAME.tar.gz"
    DBFILENAME="$OUTPUTPATH$NAME.sql.gz"
else
    # Get random file name - some secret link for downloading from magento instance :)
    MD5=`echo \`date\` $RANDOM | md5sum | cut -d ' ' -f 1`
    DATETIME=`date -u +"%Y%m%d%H%M"`
    CODEFILENAME="$OUTPUTPATH$MD5.$DATETIME.tar.gz"
    DBFILENAME="$OUTPUTPATH$MD5.$DATETIME.sql.gz"
fi

if [ -n "$MODE" ]; then
    case $MODE in
        db) createDbDump; exit 0;;
        code) createCodeDump; exit 0;;
        check) exit 0;;
        *) echo Invalid mode; exit 1;;
    esac
fi

createCodeDump
createDbDump

exit 0
