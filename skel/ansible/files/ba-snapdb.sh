#!/usr/bin/env bash

#
# ba-dumpdb.sh  -- based on magento's backup.sh
#  tool to create a database dump suitable for "snapshots"
#
#  + uses the unfortunate mysqldump, but at a nice level
#  + sanitizes [of user data &c. ] the database by default
#


# utility
#########

display_help() {
  cat <<-EOF

  A utility for snapshotting magento1 databases.

  Usage: ba-snapdb.sh <filename.sql.gz> [options]

  Example: ba-snapshot.sh ~/snapshots/dev-database.sql.gz -m ~/sites/www/webroot

             ^^^ writes snapshot to ~/snapshots/20150130.sql.gz and uses
              ~/sites/www/webroot as the magento root

  Arguments:
    --help               Display help

    -m | --mage-root     Path to Magento Root. Defaults to CWD
    -y | --no-prompt     Do not prompt for confirmation
    -s | --skip-san      Skip sanitizations


EOF

  if [ $# -eq 0 ]; then
    exit 0
  fi

  exit $1
}

error(){
  printf "\033[31m%s\n\033[0m" "$@" >&2
  exit 1
}


prompt_confirm() {
  while true; do
    read -r -n 1 -p "${1:-Continue?} [y/n]: " REPLY
    case $REPLY in
      [yY]) echo ; return 0 ;;
      [nN]) echo ; return 1 ;;
      *) printf " \033[31m %s \n\033[0m" "invalid input"
    esac
  done
}


# globals
#########

type greadlink >/dev/null 2>&1 && CWD="$(dirname "$(greadlink -f "$0")")" || \
  CWD="$(dirname "$(readlink -f "$0")")"

MAGE_ROOT="."
PROMPT=true
SANITIZE=true

# runtime
#########


_check_installed_tools() {
    local missed=""

    until [ -z "$1" ]; do
        type -t $1 >/dev/null 2>/dev/null
        if (( $? != 0 )); then
            missed="$missed $1"
        fi
        shift
    done

    echo $missed
}

checkTools() {
    REQUIRED_UTILS='nice sed tar mysqldump head gzip'
    MISSED_REQUIRED_TOOLS=`_check_installed_tools $REQUIRED_UTILS`
    if (( `echo $MISSED_REQUIRED_TOOLS | wc -w` > 0 ));
    then
        echo -e "Unable to create backup due to missing required bash tools: $MISSED_REQUIRED_TOOLS"
        exit 1
    fi
}


createDbDump() {
    # Set path of local.xml
    LOCALXMLPATH=${MAGENTOROOT}app/etc/local.xml

    # Get mysql credentials from local.xml
    getLocalValue() {
        PARAMVALUE=`sed -n "/<resources>/,/<\/resources>/p" $LOCALXMLPATH | sed -n -e "s/.*<$PARAMNAME><!\[CDATA\[\(.*\)\]\]><\/$PARAMNAME>.*/\1/p" | head -n 1`
    }

    # Connection parameters
    DBHOST=
    DBUSER=
    DBNAME=
    DBPASSWORD=
    TBLPRF=

    # Include DB logs option
    SKIPLOGS=1

    # Ignored table names
    IGNOREDTABLES="
    core_cache
    core_cache_option
    core_cache_tag
    core_session
    log_customer
    log_quote
    log_summary
    log_summary_type
    log_url
    log_url_info
    log_visitor
    log_visitor_info
    log_visitor_online
    enterprise_logging_event
    enterprise_logging_event_changes
    index_event
    index_process_event
    report_event
    report_viewed_product_index
    dataflow_batch_export
    dataflow_batch_import
    enterprise_support_backup
    enterprise_support_backup_item"

    # Sanitazed tables
    SANITIZEDTABLES="
    customer_entity
    customer_entity_varchar
    customer_address_entity
    customer_address_entity_varchar"

    # Get DB HOST from local.xml
    if [ -z "$DBHOST" ]; then
        PARAMNAME=host
        getLocalValue
        DBHOST=$PARAMVALUE
    fi

    # Get DB USER from local.xml
    if [ -z "$DBUSER" ]; then
        PARAMNAME=username
        getLocalValue
        DBUSER=$PARAMVALUE
    fi

    # Get DB PASSWORD from local.xml
    if [ -z "$DBPASSWORD" ]; then
        PARAMNAME=password
        getLocalValue
        DBPASSWORD=${PARAMVALUE//\"/\\\"}
    fi

    # Get DB NAME from local.xml
    if [ -z "$DBNAME" ]; then
        PARAMNAME=dbname
        getLocalValue
        DBNAME=$PARAMVALUE
    fi

    # Get DB TABLE PREFIX from local.xml
    if [ -z "$TBLPRF" ]; then
        PARAMNAME=table_prefix
        getLocalValue
        TBLPRF=$PARAMVALUE
    fi

    # Check DB credentials for existsing
    if [ -z "$DBHOST" -o -z "$DBUSER" -o -z "$DBNAME" ]; then
        echo "Skip DB dumping due lack of parameters host=$DBHOST; username=$DBUSER; dbname=$DBNAME;";
        exit 0
    fi

    # Set connection params
    if [ -n "$DBPASSWORD" ]; then
        CONNECTIONPARAMS=" -u$DBUSER -h$DBHOST -p\"$DBPASSWORD\" $DBNAME --force --triggers --single-transaction --opt --skip-lock-tables"
    else
        CONNECTIONPARAMS=" -u$DBUSER -h$DBHOST $DBNAME --force --triggers --single-transaction --opt --skip-lock-tables"
    fi

    # Create DB dump
    IGN_SCH=
    IGN_IGN=
    SAN_CMD=

    if $SANITIZE ; then

        for TABLENAME in $SANITIZEDTABLES; do
            SAN_CMD="$SAN_CMD $TBLPRF$TABLENAME"
            IGN_IGN="$IGN_IGN --ignore-table='$DBNAME'.'$TBLPRF$TABLENAME'"
        done
        PHP_CODE='
        while ($line=fgets(STDIN)) {
            if (preg_match("/(^INSERT INTO\s+\S+\s+VALUES\s+)\((.*)\);$/",$line,$matches)) {
                $row = str_getcsv($matches[2],",","\x27");
                foreach($row as $key=>$field) {
                    if ($field == "NULL") {
                        continue;
                    } elseif ( preg_match("/[A-Z]/i", $field)) {
                        $field = md5($field . rand());
                    }
                    $row[$key] = "\x27" . $field . "\x27";
                }
                echo $matches[1] . "(" . implode(",", $row) . ");\n";
                continue;
            }
            echo $line;
        }'
        SAN_CMD="nice -n 15 mysqldump $CONNECTIONPARAMS --skip-extended-insert $SAN_CMD | php -r '$PHP_CODE' ;"
    fi

    if [ -n "$SKIPLOGS" ] ; then
        for TABLENAME in $IGNOREDTABLES; do
            IGN_SCH="$IGN_SCH $TBLPRF$TABLENAME"
            IGN_IGN="$IGN_IGN --ignore-table='$DBNAME'.'$TBLPRF$TABLENAME'"
        done
        IGN_SCH="nice -n 15 mysqldump --no-data $CONNECTIONPARAMS $IGN_SCH ;"
    fi

    IGN_IGN="nice -n 15 mysqldump $CONNECTIONPARAMS $IGN_IGN"

    DBDUMPCMD="( $SAN_CMD $IGN_SCH $IGN_IGN) | sed -e 's/DEFINER[ ]*=[ ]*[^*]*\*/\*/' | gzip > $DBFILENAME"

    echo ${DBDUMPCMD//"p\"$DBPASSWORD\""/p[******]}

    eval "$DBDUMPCMD"
}



if [ $# -eq 0 ]; then
  display_help 1
else
  while [ $# -ne 0 ]; do
    case $1 in
      -h|--help|help)    display_help ;;
      -m|--mage-root)    MAGE_ROOT="$2" ; shift ;;
      -y|--no-prompt)    PROMPT=false ;;
      -s|--skip-san)     SANITIZE=false ;;
      *)                 DBFILENAME="$1" ;
    esac
    shift
  done

  MAGENTOROOT="$MAGE_ROOT"
  checkTools

  [ -d "$MAGE_ROOT/app/etc" ] || error \
    "missing $MAGE_ROOT/app/etc" "cd into or pass a proper magento root"

  if $PROMPT ; then
     prompt_confirm "Write snapshot to $DBFILENAME ?" || exit 0
  fi

  mkdir -p $(dirname $DBFILENAME) || error "could not write to destination dir"
  touch $DBFILENAME || error "could not write to $DBFILENAME"

  createDbDump

fi
