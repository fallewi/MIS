#!/usr/bin/env bash
# evaluates during `bin/env set` when ONSET_ACTIVE is truthy.

# @TODO dry run?

APP_URL_LOWER=$(echo $APP_URL | awk '{print tolower($0)}')

# autodetect n98 path
N98_TESTS="n98-magerun
n98-magerun.phar
n98
n98.phar"

for N98_BIN in $N98_TESTS; do
  type $N98_BIN >/dev/null 2>&1 || continue

  # n98 commands
  ##############

  cd $APP_ROOT

  # We should probably consider sites with multiple storeviews, all these values can be storeview specific

  $N98_BIN config:set web/unsecure/base_url $APP_URL_LOWER
  $N98_BIN config:set web/secure/base_url $APP_URL_LOWER

  # If they have a CDN configured, the following URLs could be different and break things. Best to assert their values
  $N98_BIN config:set web/unsecure/base_link_url "{{unsecure_base_url}}"
  $N98_BIN config:set web/unsecure/base_skin_url "{{unsecure_base_url}}skin/"
  $N98_BIN config:set web/unsecure/base_media_url "{{unsecure_base_url}}media/"
  $N98_BIN config:set web/unsecure/base_js_url "{{unsecure_base_url}}js/"
  $N98_BIN config:set web/secure/base_link_url "{{secure_base_url}}"
  $N98_BIN config:set web/secure/base_skin_url "{{secure_base_url}}skin/"
  $N98_BIN config:set web/secure/base_media_url "{{secure_base_url}}media/"
  $N98_BIN config:set web/secure/base_js_url "{{secure_base_url}}js/"

  # This will remove any admin url configurations in the db so it will work natively from the settings in app/etc/local.xml
  $N98_BIN config:delete admin/url/custom
  $N98_BIN config:set admin/url/use_custom 0
  $N98_BIN config:set admin/url/use_custom_path 0

  # Relaxing security
  $N98_BIN config:set admin/security/session_cookie_lifetime 86400
  $N98_BIN config:set admin/security/password_is_forced 0
  $N98_BIN config:set admin/security/password_lifetime 1000

  # Turn on symlinks
  $N98_BIN config:set dev/template/allow_symlink 1

  # Turn off merging
  $N98_BIN config:set dev/js/merge_files 0
  $N98_BIN config:set dev/css/merge_files 0

  # this will set the domain for all cookies Magento generate, a lot of times this is set. It usually works fine
  # if it's an empty string
  $N98_BIN config:set web/cookie/cookie_domain ""
  
  # break loop, no need to continue n98 tests
  break
done


####
# lazily seed modules.enabled|disabled files from BOILERPLATE
for file in modules.enabled modules.disabled; do
  [ -e $ENV_DIR/$file ] || cp $BOILERPLATE_DIR/env/$file $ENV_DIR/$file
done


# sed_inplace : in place file substitution, because darwin hates GPLv3
######################################################################
#
# usage: sed_inplace "file" "sed substitution"
#    ex: sed_inplace "/tmp/file" "s/CLIENT_CODE/BA/g"
#

sed_inplace(){
  # linux
  local SED_CMD="sed"

  if [[ $OSTYPE == darwin* ]]; then
    if $(type gsed >/dev/null 2>&1); then
      local SED_CMD="gsed"
    elif $(type /usr/local/bin/sed >/dev/null 2>&1); then
      local SED_CMD="/usr/local/bin/sed"
    else
      sed -i '' -E "$2" $1
      return
    fi
  fi

  $SED_CMD -r -i "$2" $1
}


# enable modules
################
for modfile in $ENV_DIR/$ENV/modules.enabled $ENV_DIR/modules.enabled; do
  [ -e $modfile ] || continue;

  echo "  parsing $modfile..."

  for xmlfile in $(grep -vE '^\s*(#|$)' $modfile); do
    xmlfile=$APP_ROOT/app/etc/modules/$xmlfile
    if [ -e $xmlfile ]; then
      echo "     activating modules defined in $(basename $xmlfile)"
      git update-index --assume-unchanged $xmlfile
      sed_inplace "$xmlfile" "s/(<active>).*(<\/active>)/\1true\2/g"
    else
      echo "     ENOENT $xmlfile - skipping..."
    fi
  done

  # break loop, do not continue to next file
  break
done

# disable modules
#################
for modfile in $ENV_DIR/$ENV/modules.disabled $ENV_DIR/modules.disabled; do
  [ -e $modfile ] || continue;

  echo "  parsing $modfile..."

  for xmlfile in $(grep -vE '^\s*(#|$)' $modfile); do
    xmlfile=$APP_ROOT/app/etc/modules/$xmlfile
    if [ -e $xmlfile ]; then
      echo "     deactivating modules defined in $(basename $xmlfile)"
      git update-index --assume-unchanged $xmlfile
      sed_inplace "$xmlfile" "s/(<active>).*(<\/active>)/\1false\2/g"
    else
      echo "     ENOENT $xmlfile - skipping..."
    fi
  done

  # break loop, do not continue to next file
  break
done
