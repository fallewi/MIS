#!/usr/bin/env python

'''
BlueAcorn external inventory script
===================================

Generates Ansible inventory from an ssh_config file. Example:

If you have in your .ssh/config:
  Host terminator
    HostName terminator.blueacorn.net
    User blueacorn
    IdentityFile /path/to/id_rsa

You can do
  $ ansible terminator -m ping


based on
  http://docs.ansible.com/ansible/developing_inventory.html
'''

######################################################################

import os
import sys
import argparse
import ConfigParser

import paramiko
import re

from subprocess import Popen, PIPE

try:
    import json
except ImportError:
    import simplejson as json


class BlueAcornInventory(object):

    ###########################################################################
    # Main execution path
    ###########################################################################

    def __init__(self):
        ''' Main execution path '''
        self.ssh_conf_file = '~/.ssh/config'
        self.hostgroup = 'all'
        self.envars_files = None

        # Read settings, environment variables, and CLI arguments
        self.read_environment()
        self.read_cli_args()

        # Read passed envars files
        ##########################
        vars = {}
        if self.envars_files:
          cmd = "env -i bash".split()
          input = "set -a"

          for envfile in [file.strip() for file in self.envars_files.split(',')]:
            if os.path.isfile(os.path.expanduser(envfile)):
              input += " && source " + envfile

          input += "&& printenv"

          p = Popen(cmd, stdout=PIPE, stdin=PIPE, stderr=PIPE)
          stdout, stderr = p.communicate(input=input)
          if not stderr:
            ignores = ['PWD', 'SHLVL', '_']
            for line in stdout.splitlines():
              match = re.match(r'^\s*([^#].*?)="?(.*?)"?\s*$', line)
              if match and match.group(1) not in ignores:
                vars[match.group(1)] = match.group(2)

        # generate inventory
        ####################

        self.inventory = {
          self.hostgroup: {
            "hosts": [],
            "vars": vars
          },
          "_meta": {
            "hostvars": {}
          }
        }

        # export vars to all groups as well
        if self.hostgroup != 'all':
            self.inventory['all'] = {'vars': vars}

        # read inventory from ssh config
        ##################################

        cfg = self.parse_config()
        for hostname, host in cfg.items():
          self.inventory[self.hostgroup]["hosts"].append(hostname)
          self.inventory["_meta"]["hostvars"][hostname] = self.transmorg([
            host.get("hostname"),
            host.get("user"),
            host.get("port"),
            host.get("identityfile")
          ], [
            'ansible_ssh_host',
            'ansible_ssh_user',
            'ansible_ssh_port',
            'ansible_ssh_private_key_file'
          ])

        # provide localhost host
        self.inventory[self.hostgroup]["hosts"].append("localhost")
        self.inventory["_meta"]["hostvars"]["localhost"] = self.transmorg([
            'local'
          ], [
            'ansible_connection'
          ])

        if self.args.host:
          inventory = self.inventory._meta.hostvars[self.args.host]
        else:
          inventory = self.inventory

        if self.args.pretty:
            print json.dumps(inventory, sort_keys=True, indent=2)
        else:
            print json.dumps(inventory)

        sys.exit()


    ###########################################################################
    # Script configuration
    ###########################################################################

    def read_environment(self):
        ''' Reads the settings from environment variables '''
        if os.getenv("ANSIBLE_SSH_CONF_FILE"): self.ssh_conf_file = os.getenv("ANSIBLE_SSH_CONF_FILE")
        if os.getenv("ANSIBLE_SSH_CONF_ENVARS"): self.envars_files = os.getenv("ANSIBLE_SSH_CONF_ENVARS")
        if os.getenv("ANSIBLE_SSH_CONF_HOSTGROUP"): self.hostgroup = os.getenv("ANSIBLE_SSH_CONF_HOSTGROUP")


    def read_cli_args(self):
        ''' Command line argument processing '''
        parser = argparse.ArgumentParser(description='Produce an Ansible Inventory from an ssh config file')

        parser.add_argument('--pretty', '-p', action='store_true', help='Pretty-print results')
        parser.add_argument('--conf-file', '-f', action='store', help='Path to ssh config file, default to ANSIBLE_SSH_CONF_FILE environment variable, or "~/.ssh/config" if not set.')
        parser.add_argument('--envars', '-v', action='store', help='Path to envars file(s) storing variables in shell format. defaults to ANSIBLE_SSH_CONF_ENVARS environment variable. Separate with a comma, evaluated left-to-right')


        parser.add_argument('--list', action='store_true', help='List all active Hosts (default: True)')
        parser.add_argument('--host', action='store', help='Get all Ansible inventory variables about a specific Host')

        self.args = parser.parse_args()

        if self.args.conf_file: self.ssh_conf_file = self.args.conf_file
        if self.args.envars: self.envars_files = self.args.envars


    ###########################################################################
    # Utility
    ###########################################################################

    # via https://github.com/ansible/ansible/blob/devel/contrib/inventory/ssh_config.py
    def parse_config(self):
      if not os.path.isfile(os.path.expanduser(self.ssh_conf_file)):
          return {}
      with open(os.path.expanduser(self.ssh_conf_file)) as f:
          cfg = paramiko.SSHConfig()
          cfg.parse(f)
          ret_dict = {}
          for d in cfg._config:
              if type(d['host']) is list:
                  alias = d['host'][0]
              else:
                  alias = d['host']
              if ('?' in alias) or ('*' in alias):
                  continue
              _copy = dict(d)
              del _copy['host']
              if 'config' in _copy:
                  ret_dict[alias] = _copy['config']
              else:
                  ret_dict[alias] = _copy
          return ret_dict

    def transmorg(self, data, keys):

      output = {}
      values = [value for value in data]

      for i, value in enumerate(values):
          if (value):
              output[keys[i]] = value

      return output


# Run the script
BlueAcornInventory()
