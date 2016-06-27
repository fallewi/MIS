#!/usr/bin/env python

'''
BlueAcorn external inventory script
===================================

Generates Ansible inventory for dockerized environments. Requires ansible 2.0

host "docker-machine" will point to DOCKER_MACHINE defined by envars, e.g.
  ocean : ocean-SIG
  qa-1  : node-a
  qa-2  : node-b

host "term-container" will point to the terminal container (docker connection)

based on
  http://docs.ansible.com/ansible/developing_inventory.html
'''

######################################################################

import os
import sys
import argparse

import subprocess
import re

try:
    import json
except ImportError:
    import simplejson as json


def dm(*args):
    return subprocess.check_output(["docker-machine"] + list(args)).strip()

def dminspect(fmt, mcn, default=''):
    val = dm("inspect", "-f", fmt, mcn)
    return default if not val else val

class BlueAcornInventory(object):

    ###########################################################################
    # Main execution path
    ###########################################################################

    def __init__(self):
        ''' Main execution path '''
        self.hostgroup = 'all'
        self.envars_files = None

        # Read settings, environment variables, and CLI arguments
        self.read_environment()
        self.read_cli_args()

        self.inventory = {
          self.hostgroup: {
            "hosts": [],
            "vars": {}
          },
          "_meta": {
            "hostvars": {}
          }
        }

        # Read passed envars files
        ##########################
        if self.envars_files:
          vars = {}
          cmd = "env -i bash".split()
          input = "set -a"

          for envfile in [file.strip() for file in self.envars_files.split(',')]:
            if os.path.isfile(os.path.expanduser(envfile)):
              input += " && source " + envfile

          input += "&& printenv"

          p = subprocess.Popen(cmd, stdout=subprocess.PIPE, stdin=subprocess.PIPE, stderr=subprocess.PIPE)
          stdout, stderr = p.communicate(input=input)
          if not stderr:
            ignores = ['PWD', 'SHLVL', '_']
            for line in stdout.splitlines():
              match = re.match(r'^\s*([^#].*?)="?(.*?)"?\s*$', line)
              if match and match.group(1) not in ignores:
                vars[match.group(1)] = match.group(2)

            self.inventory[self.hostgroup]['vars'] = vars

        # generate inventory
        ####################

        # provide localhost host
        self.inventory[self.hostgroup]["hosts"].append("localhost")
        self.inventory["_meta"]["hostvars"]["localhost"] = self.transmorg([
            'local'
          ], [
            'ansible_connection'
          ])

        if 'DOCKER_MACHINE' not in vars:
            print "DOCKER_MACHINE is not exported in envars"
            exit()

        machine_name = vars["DOCKER_MACHINE"]
        storepath = dminspect("{{.Driver.StorePath}}", machine_name)

        self.inventory[self.hostgroup]["hosts"].append("docker-machine")
        self.inventory["_meta"]["hostvars"]["docker-machine"] = self.transmorg([
            dminspect("{{.Driver.IPAddress}}", machine_name),
            dminspect("{{.Driver.SSHUser}}", machine_name, 'root'),
            dminspect("{{.Driver.SSHPort}}", machine_name, '22'),
            dminspect("{{.Driver.SSHKeyPath}}", machine_name, "%s/machines/%s/id_rsa" % (storepath, machine_name))
          ], [
            'ansible_ssh_host',
            'ansible_ssh_user',
            'ansible_ssh_port',
            'ansible_ssh_private_key_file'
          ])

        # @TODO blow up if CLIENT_CODE not provided
        container_name = "%s-%s-term" % (self.hostgroup, \
          self.inventory[self.hostgroup]['vars']['CLIENT_CODE'])

        self.inventory[self.hostgroup]["hosts"].append("term-container")
        self.inventory["_meta"]["hostvars"]["term-container"] = self.transmorg([
            container_name,
            "docker"
          ], [
            'ansible_host',
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
        if os.getenv("ANSIBLE_SSH_CONF_ENVARS"): self.envars_files = os.getenv("ANSIBLE_SSH_CONF_ENVARS")
        if os.getenv("ANSIBLE_SSH_CONF_HOSTGROUP"): self.hostgroup = os.getenv("ANSIBLE_SSH_CONF_HOSTGROUP")


    def read_cli_args(self):
        ''' Command line argument processing '''
        parser = argparse.ArgumentParser(description='Produce an Ansible Inventory from an ssh config file')

        parser.add_argument('--pretty', '-p', action='store_true', help='Pretty-print results')
        parser.add_argument('--envars', '-v', action='store', help='Path to envars file(s) storing variables in shell format. defaults to ANSIBLE_SSH_CONF_ENVARS environment variable. Separate with a comma, evaluated left-to-right')


        parser.add_argument('--list', action='store_true', help='List all active Hosts (default: True)')
        parser.add_argument('--host', action='store', help='Get all Ansible inventory variables about a specific Host')

        self.args = parser.parse_args()

        if self.args.envars: self.envars_files = self.args.envars


    ###########################################################################
    # Utility
    ###########################################################################

    def transmorg(self, data, keys):

      output = {}
      values = [value for value in data]

      for i, value in enumerate(values):
          if (value):
              output[keys[i]] = value

      return output


# Run the script
BlueAcornInventory()
