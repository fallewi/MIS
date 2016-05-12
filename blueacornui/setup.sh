#!/usr/bin/env bash

#/**
# * @package     BlueAcorn/GreenPistachio
# * @version     4.3.0
# * @author      Blue Acorn, Inc. <code@blueacorn.com>
# * @copyright   Copyright © 2016 Blue Acorn, Inc.
# */

###########
# Utility #
###########

display_help () {

cat <<-EOF

  Utility for Managing Green Pistachio Workflow

  Usage: setup.sh <command> [options]

  Commands:
    dev     Installs Dependancies.
    site    Setting up a Site/Repo for the First Time
    update  Update Dependancies if they've Changed

  Options:
    None at this time.

EOF

  if [ $# -eq 0 ]; then
    exit 0
  fi

  exit $1

}

error () {
  echo -en >&2 "\033[31m"
  echo -e >&2 "$@"
  echo -en >&2 "\033[0m"
  exit 1
}

############
# Workflow #
############

reset_blueacorn_ui () {

  # Used for a Fresh Start
  sudo rm -rf node_modules bower_components app skin

}

install_dependancies () {

  # Install Node Modules
  sudo npm install

  # Install Bower Components
  bower install

}

# Setting up Green Pistachio for the Dev First Time
workflow_dev_setup () {

  # Updating
  workflow_update

  # Compiling
  grunt compile

}

# Setting up Green Pistachio for the Repo First Time
workflow_site_setup () {

  if [ ! -d ../webroot ]; then
    cd ../
  else
    cd ../webroot
  fi

  modman deploy --copy green-pistachio --force

  if [ ! -d ../webroot ]; then
    cd blueacornui
  else
    cd ../blueacornui
  fi

  # Bringing in the New
  install_dependancies

  # Setting up the Site
  grunt setup

}

workflow_update () {

  # Clearing out the Old
  reset_blueacorn_ui

  # Bringing in the New
  install_dependancies

}

###########
# Runtime #
###########

runstr="display_help"

if [ $# -eq 0 ]; then
  display_help 1
else
  while [ $# -ne 0 ]; do
    case $1 in
      -h|--help|help)    display_help ;;
      dev)         runstr="workflow_dev_setup" ; shift ;;
      site)        runstr="workflow_site_setup" ; shift ;;
      update)            runstr="workflow_update" ;;
    esac
    shift
  done

  $runstr
fi
