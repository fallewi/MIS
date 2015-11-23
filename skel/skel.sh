#!/usr/bin/env bash

# utility
#########

display_help() {
  cat <<-EOF

  A utility for syncing configuration changes with the skel/ branch

  Usage: skel.sh <command> [options]

  Commands:
    push                 Push working copy changes to the skel/ branch

    pull                 Pull latest from skel/ branch into the working copy

    create               Create the skel/ branch

  Options:
    --help               Display help

    --diff | -d          Prints the diff of changes to be applied.
                         Effectively a dry-run / preview mode

    --no-prompt | -np    Avoids prompts
EOF

  if [ $# -eq 0 ]; then
    exit 0
  fi

  exit $1
}

error(){
  printf "\033[31m%s\n\033[0m" "$@" >&2
  git checkout $WORKING_BRANCH
  exit 1
}

# globals
#########

type greadlink >/dev/null 2>&1 && CWD="$(dirname "$(greadlink -f "$0")")" || \
  CWD="$(dirname "$(readlink -f "$0")")"
REPO_ROOT=`git rev-parse --show-toplevel`

SKEL_DIR="skel/"

SKEL_BRANCH="skel/configuration"
WORKING_BRANCH=$(git rev-parse --abbrev-ref HEAD)

SKEL_TREE="$SKEL_BRANCH:$SKEL_DIR"
WORKING_TREE="$WORKING_BRANCH:$SKEL_DIR"

[ "$WORKING_BRANCH" = "$SKEL_BRANCH" ] && error \
  "you cannot run this script from the $SKEL_BRANCH branch." \
  "please commit any changes and checkout a working branch (like -develop)"


# defaults
FORCE_OVERWRITE=${INIT_FORCE_OVERWRITE:-false}
SKIP_PROMPTS=${INIT_SKIP_PROMPTS:-false}
DRY_RUN=${INIT_DRY_RUN:-false}

# run functions
###############

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

ensure_clean_skel() {
  git fetch origin $SKEL_BRANCH
  git checkout $SKEL_BRANCH || error "error checking out local skel branch!"

  git merge --no-commit --ff-only origin/$SKEL_BRANCH
  local CLEAN_MERGE=$?
  git merge --abort

  [ $CLEAN_MERGE -eq 0 ] || error "you have unpushed changes in $SKEL_BRANCH"

  git merge --ff-only origin/$SKEL_BRANCH
  git checkout $WORKING_BRANCH
}


run_push() {

  ensure_clean_skel

  git checkout $SKEL_BRANCH

  local DIFF_CMD="git diff-tree -p $SKEL_TREE $WORKING_TREE"

  if $DRY_RUN ; then
    $DIFF_CMD
    git checkout $WORKING_BRANCH
    exit
  fi

  $DIFF_CMD | git apply --directory=$SKEL_DIR || error "no changes to push"

  echo
  echo "pushed changes from $WORKING_BRANCH into $SKEL_BRANCH"
  echo

  git add $SKEL_DIR
  git commit -m "pushed changes from $WORKING_BRANCH into $SKEL_BRANCH"
  git checkout $WORKING_BRANCH

  $SKIP_PROMPTS && return

  prompt_confirm "push changes to origin?" || return
  git push origin $SKEL_BRANCH
}

run_pull(){

  ensure_clean_skel

  local DIFF_CMD="git diff-tree -p $WORKING_TREE $SKEL_TREE"

  if $DRY_RUN ; then
    $DIFF_CMD
    exit
  fi

  $DIFF_CMD | git apply --directory=$SKEL_DIR || error "no changes to pull"

  echo
  echo "pulled changes from $SKEL_BRANCH into $WORKING_BRANCH"
  echo

  $SKIP_PROMPTS && return

  git add $SKEL_DIR
  git commit -m "pulled changes from $SKEL_BRANCH into $WORKING_BRANCH"

  prompt_confirm "push changes to origin?" || return
  git push origin $WORKING_BRANCH
}

run_create(){
  git rev-parse --verify origin/$SKEL_BRANCH >/dev/null 2>&1
  if [ $? -eq 0 ]; then
    error "the skel branch ($SKEL_BRANCH) already exists"
  fi

  git checkout --orphan $SKEL_BRANCH
  git rm -rf *
  git rm .watchlist -f
  git clean -df
  git clean -df

  git read-tree --prefix=$SKEL_DIR -u $WORKING_TREE
  git commit -m "initial commit on $SKEL_BRANCH"
  git checkout $WORKING_BRANCH

  echo
  echo "skel branch ($SKEL_BRANCH) has been created "
  echo

  $SKIP_PROMPTS || prompt_confirm "push branch to origin?" || return

  git push origin $SKEL_BRANCH
}

# runtime
#########

runstr="display_help"

cd $REPO_ROOT

if [ $# -eq 0 ]; then
  display_help 1
else
  while [ $# -ne 0 ]; do
    case $1 in
      -h|--help|help)    display_help ;;
      --force|-f)        FORCE_OVERWRITE=true ;;
      --no-prompt|-np)   SKIP_PROMPTS=true ;;
      --diff|-d)         DRY_RUN=true ;;
      push)              runstr="run_push" ;;
      pull)              runstr="run_pull" ;;
      create)            runstr="run_create" ;;
      *)                 echo "invalid option: $1" ; display_help 1 ;;
    esac
    shift
  done

  $runstr
fi
