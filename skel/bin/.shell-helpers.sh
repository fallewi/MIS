#!/bin/sh
#
# shell-helpers version v1.0.0-pr build a805687
# https://github.com/briceburg/shell-helpers
# copyright 2016-2016 Brice Burgess - Licensed under the Apache License 2.0
#

# shell_detect - detect user's shell and sets
#  __shell (user's shell, e.g. 'fish', 'bash', 'zsh')
#  __shell_file (shell configuration file, e.g. '~/.bashrc')
# usage: shell_detect [shell (skips autodetect)]
shell_detect(){
  # https://github.com/rbenv/rbenv/wiki/Unix-shell-initialization
  __shell=${1:-$(basename $SHELL | awk '{print tolower($0)}')}
  __shell_file=

  local search=
  case $__shell in
    bash|sh   ) search=".bashrc .bash_profile" ;;
    cmd       ) search=".profile" ;;
    ash|dash  ) search=".profile" ;;
    fish      ) search=".config/fish/config.fish" ;;
    ksh       ) search=".kshrc" ;;
    powershell) search=".profile" ;;
    tcsh      ) search=".tcshrc .cshrc .login" ;;
    zsh       ) search=".zshenv .zprofile .zshrc" ;;
    *         ) error_exception "unrecognized shell \"$__shell\"" ;;
  esac

  for file in $search; do
    [ -e ~/$file ] && {
      __shell_file=~/$file
      return 0
    }
  done

  __shell_file=~/.profile
  echo "# failed to detect shell config file, falling back to $__shell_file"
  return 1
}

# shell_eval_export - print evaluable commands to export a variable
# usage: shell_eval_export <variable> <value> [append_flag] [append_delim]
shell_eval_export(){
  local append=${3:-false}
  local append_delim=$4
  [ "$1" = "PATH" ] && [ -z "$append_delim" ] && append_delim=':'

  if $append; then
    case $__shell in
      cmd       ) echo "SET $1=%${1}%${append_delim}${2}" ;;
      fish      ) echo "set -gx $1 \$${1} ${2};" ;;
      tcsh      ) echo "setenv $1 = \$${1}${append_delim}${2}" ;;
      powershell) echo "\$Env:$1 = \"\$${1}${append_delim}${2}\";" ;;
      *         ) echo "export $1=\"\$${1}${append_delim}${2}\"" ;;
    esac
  else
    case $__shell in
      cmd       ) echo "SET $1=$2" ;;
      fish      ) echo "set -gx $1 \"$2\";" ;;
      tcsh      ) echo "setenv $1 \"$2\"" ;;
      powershell) echo "\$Env:$1 = \"$2\";" ;;
      *         ) echo "export $1=\"$2\"" ;;
    esac
  fi

  shell_eval_message
}

shell_eval_message(){
  #@TODO transform entrypoint to absolute path

  local pre
  local post

  case $__shell in
    cmd       ) pre="@FOR /f "tokens=*" %i IN ('" post="') DO @%i'" ;;
    fish      ) pre="eval (" post=")" ;;
    tcsh      ) pre="eval \`" post="\`" ;;
    powershell) pre="&" post=" | Invoke-Expression" ;;
    *         ) pre="eval \$(" ; post=")" ;;
  esac

  echo "# To configure your shell, run:"
  echo "#   ${pre}${__entrypoint}${post}"
  echo "# To remember your configuration in subsequent shells, run:"
  echo "#   echo ${pre}${__entrypoint}${post} >> $__shell_file"
}
__local_docker(){
  (
    __deactivate_machine
    exec docker "$@"
  )
}

__deactivate_machine(){
  # @TODO support boot2docker / concept of "default" machine
  type docker-machine &>/dev/null && {
    eval $(docker-machine env --unset --shell bash)
    return
  }
  # lets be safe and unset if missing docker-machine
  unset DOCKER_HOST DOCKER_TLS_VERIFY DOCKER_CERT_PATH DOCKER_MACHINE_NAME
}

docker_safe_name(){
  local name="$@"
  set -- "${name:0:1}" "${name:1}"
  printf "%s%s" "${1//[^a-zA-Z0-9]/0}" "${2//[^a-zA-Z0-9_.-]/_}"
}
#
# lib.d/helpers/git.sh for dex -*- shell-script -*-
#

error(){
  [ -z "$1" ] && set -- "general exception. halting..."

  printf "\e[31m%b\n\e[0m" "$@" >&2
  exit ${__error_code:-1}
}

error_noent() {
  __error_code=127
  error "$@"
}

error_perms() {
  __error_code=126
  error "$@"
}

error_exception() {
  __error_code=2
  error "$@"
}


log(){
  printf "\e[33m%b\n\e[0m" "$@" >&2
}

warn(){
  printf "\e[35m%b\n\e[0m" "$@" >&2
}

prompt_echo() {
  while true; do
    # read always from /dev/tty, use `if [ -t 0 ]` upstream to avoid prompt
    read -r -p "  ${1:-input} : " INPUT </dev/tty
    [ -z "$INPUT" ] || { echo "$INPUT" ; return 0 ; }
    printf "  \033[31m%s\033[0m\n" "invalid input" >&2
  done
}

prompt_confirm() {
  while true; do
    echo
    # read always from /dev/tty, use `if [ -t 0 ]` upstream to avoid prompt
    read -r -n 1 -p "  ${1:-Continue?} [y/n]: " REPLY </dev/tty
    case $REPLY in
      [yY]) echo ; return 0 ;;
      [nN]) echo ; return 1 ;;
      *) printf "  \033[31m%s\033[0m\n" "invalid input" >&2
    esac
  done
}

# line_in_file : ensure a line exists in a file
###############################################
#
# usage: line_in_file "file" "match" "line"
#    ex: line_in_file "varsfile" "^VARNAME=.*$" "VARNAME=value"
#
line_in_file(){
  local delim=${4:-"|"}
  grep -q "$2" $1 2>/dev/null && sed_inplace $1 "s$delim$2$delim$3$delim" || echo $3 >> $1
}

# get_group_id accepts <group_name> and outputs group id, empty if not found.
get_group_id(){
  if type getent &>/dev/null; then
    getent group $1 | cut -d: -f3
  elif type dscl &>/dev/null; then
    dscl . -read /Groups/$1 PrimaryGroupID 2>/dev/null | awk '{ print $2 }'
  else
    python -c "import grp; print(grp.getgrnam(\"$1\").gr_gid)" 2>/dev/null
  fi
}


# sed_inplace : in place file substitution
############################################
#
# usage: sed_inplace "file" "sed substitution"
#    ex: sed_inplace "/tmp/file" "s/CLIENT_CODE/BA/g"
#
sed_inplace(){
  # linux
  local __sed="sed"

  if [[ "$OSTYPE" == darwin* ]] || [[ "$OSTYPE" == macos* ]] ; then
    if $(type gsed >/dev/null 2>&1); then
      local __sed="gsed"
    elif $(type /usr/local/bin/sed >/dev/null 2>&1); then
      local __sed="/usr/local/bin/sed"
    else
      sed -i '' -E "$2" $1
      return
    fi
  fi

  $__sed -r -i "$2" $1
}
#
# lib.d/helpers/git.sh for dex -*- shell-script -*-
#

# usage: clone_or_pull <repo-path-or-url> <destination> <force boolean>
clone_or_pull(){
  local force=${3:-false}
  if [ -d $2 ]; then
    # pull
    (
      cd $2
      $force && git reset --hard HEAD
      git pull
    ) || {
      log "error pulling changes from git"
      return 1
    }
  else
    # clone

    #@TODO support reference repository
    #  [detect if local repo is a bare repo -- but how to find remote?]

    local SHARED_FLAG=

    [ -w $(dirname $2) ] || {
      log "destination directory not writable"
      return 126
    }

    if [[ $1 == /* ]]; then
      # perform a shared clone (URL is a local path starting with '/...' )
      [ -d $1/.git ] || {
        log "$1 is not a path to a local git repository"
        return 1
      }
      SHARED_FLAG="--shared"
    fi

    git clone $SHARED_FLAG $1 $2 || {
      log "error cloning $1 to $2"
      return 1
    }
  fi

  return 0
}


# checks git working copy.
# return 1 if clean (not dirty), 0 if dirty (changes exist)
is_dirty(){

  [ -d $1/.git ] || {
    log "$1 is not a git repository. continuing..."
    return 1
  }

  (
    set -e
    cd $1
    [ ! -z "$(git status -uno --porcelain)" ]
  )
  return $?
}
#
# lib.d/helpers/cli.sh for dex -*- shell-script -*-
#

# normalize_flags - normalize POSIX short and long flags for easier parsing
# usage: normalize_flags <fargs> [<flags>...]
#   <fargs>: string of short flags requiring an argument.
#   <flags>: flag string(s) to normalize, typically passed as "$@"
# examples:
#   normalize_flags "" "-abc"
#     => -a -b -c
#   normalize_flags "om" "-abcooutput.txt" "--def=jam" "-mz"
#     => -a -b -c -o output.txt --def jam -m z"
#   normalize_flags "om" "-abcooutput.txt" "--def=jam" "-mz" "--" "-abcx" "-my"
#     => -a -b -c -o output.txt --def jam -m z -- -abcx -my"
normalize_flags(){
  local fargs="$1"
  local passthru=false
  local output=""
  shift
  for arg in $@; do
    if $passthru; then
      output+=" $arg"
    elif [ "--" = "$arg" ]; then
      passthru=true
      output+=" --"
    elif [ "--" = ${arg:0:2} ]; then
      output+=" ${arg%=*}"
      [[ "$arg" == *"="* ]] && output+=" ${arg#*=}"
    elif [ "-" = ${arg:0:1} ]; then
      local p=1
      while ((p++)); read -n1 flag; do
        [ -z "$flag" ] || output+=" -$flag"
        if [[ "$fargs" == *"$flag"* ]]; then
          output+=" ${arg:$p}"
          break
        fi
      done < <(echo -n "${arg:1}")
    else
      output+=" $arg"
    fi
  done
  printf "%s" "${output:1}"
}

# normalize_flags_first - like normalize_flags, but outputs flags first.
# usage: normalize_flags <fargs> [<flags>...]
#   <fargs>: string of short flags requiring an argument.
#   <flags>: flag string(s) to normalize, typically passed as "$@"
# examples:
#   normalize_flags_first "" "-abc command -xyz otro"
#     => -a -b -c -x -y -z command otro
#   normalize_flags_first "" "-abc command -xyz otro -- -def xyz"
#     => -a -b -c -x -y -z command otro -- -def xyz

normalize_flags_first(){
  local fargs="$1"
  local output=""
  local cmdstr=""
  local passthru=false
  shift
  for arg in $(normalize_flags "$fargs" "$@"); do
    [ "--" = "$arg" ] && passthru=true
    if $passthru || [ ! "-" = ${arg:0:1} ]; then
      cmdstr+=" $arg"
      continue
    fi
    output+=" $arg"
  done
  printf "%s%s" "${output:1}" "$cmdstr"
}

# set_cmd: loops through a list of commands, prefering the "prefixed" version(s)
#   sets `__cmd` to first-found matching command. uses __cmd_prefix
#   returns 1 if no suitable command found.
set_cmd(){
  __cmd=
  local path=
  for lookup in $@; do
    type ${__cmd_prefix}${lookup} &>/dev/null && {
      __cmd=${__cmd_prefix}${lookup}
      return 0
    }
  done

  for lookup in $@; do
    type $lookup &>/dev/null && {
      __cmd=$lookup
      return 0
    }
  done

  return 1
}

runfunc(){
  [ "$(type -t $1)" = "function" ] || error \
    "$1 is not a valid runfunc target"

  eval "$@"
}

unrecognized_flag(){
  printf "\n\n$1 is an unrecognized flag\n\n"
  display_help 2
}

unrecognized_arg(){

  if [ $__cmd = "main" ]; then
    printf "\n\n$1 is an unrecognized command\n\n"
  else
    printf "\n\n$1 is an unrecognized argument\n\n"
  fi

  display_help 2
}
#
# lib.d/helpers/network.sh for dex -*- shell-script -*-
#

# usage: fetch-url <url> <target-path>
fetch-url(){
  local WGET_PATH=${WGET_PATH:-wget}
  local CURL_PATH=${CURL_PATH:-curl}

  if ( type $WGET_PATH &>/dev/null ); then
    $WGET_PATH $1 -qO $2 || ( rm -rf $2 ; exit 1 )
  elif ( type $CURL_PATH &>/dev/null ); then
    $CURL_PATH -Lfso $2 $1
  else
    log "failed to fetch $2 from $1" "missing both curl and wget"
    return 2
  fi

  [ $? -eq 0 ] && return 0

  log "failed to fetch $2 from $1"
  return 126
}
