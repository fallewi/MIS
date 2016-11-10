from __future__ import (absolute_import, division, print_function)
__metaclass__ = type

import os
import urllib
import json
import socket
import sys
import traceback


from ansible.plugins.callback import CallbackBase
from ansible.module_utils.urls import open_url
from ansible.module_utils.urls import fetch_url

from datetime import datetime
from getpass import getuser
from time import time

from subprocess import check_output, CalledProcessError

class CallbackModule(CallbackBase):
    """This is an example ansible callback plugin that sends status
    updates to a HipChat channel during playbook execution.
    This plugin makes use of the following environment variables:
        HIPCHAT_TOKEN (required): HipChat API token
        HIPCHAT_ROOM  (optional): HipChat room to post in. Default: ansible
        HIPCHAT_FROM  (optional): Name to post as. Default: ansible
        HIPCHAT_NOTIFY (optional): Add notify flag to important messages ("true" or "false"). Default: true
    """
    CALLBACK_VERSION = 2.0
    CALLBACK_TYPE = 'notification'
    CALLBACK_NAME = 'hipchat'
    CALLBACK_NEEDS_WHITELIST = False

    def __init__(self):

        super(CallbackModule, self).__init__()

        self.id = "hipchatty-%i" % (time())
        self.start_time = datetime.now()
        self.playbook_name = None
        self.client_code = os.getenv('CLIENT_CODE', 'unknown')
        self.environment = os.getenv('ENV', 'unknown')

        self.from_name = '%s@%s' % (getuser(), socket.gethostname())
        self.token = os.getenv('HIPCHAT_TOKEN', 'B3QQ016elG65pEIPFDhALCAJzTy1Edcoh6uTybJe')
        self.msg_uri = 'https://api.hipchat.com/v2/room/%s/notification' % os.getenv('HIPCHAT_ROOM', 'devops-bottalk')
        if self.token is None:
            self.disabled = True
            self._display.warning('HipChat token could not be loaded. The HipChat '
                          'token can be provided using the `HIPCHAT_TOKEN` '
                          'environment variable.')

    def days_hours_minutes_seconds(self, runtime):
        minutes = (runtime.seconds // 60) % 60
        r_seconds = runtime.seconds - (minutes * 60)
        return runtime.days, runtime.seconds // 3600, minutes, r_seconds

    def git_root_path(self, cwd):
        """ return absolute path of the repository root, or empty string if not in git repository """
        try:
            base = check_output('git rev-parse --show-toplevel', shell=True, cwd=cwd )
        except CalledProcessError:
            return ""
        return base.decode('utf-8').strip()

    def make_card(self, notification, description, attributes=[]):
        card = {
            'style': "application",
            'format': "medium",
            'id': self.id,
            'title': self.playbook_name,
            'description': {
                'value': description,
                'format': "html"
            },
            'attributes': [
                {
                'label': "client / env",
                'value': {
                    'label': '%s / %s ' % (self.client_code, self.environment),
                    'style': "lozenge-complete"
                    }
                }
            ],
            'activity': {
                'html': notification
            }
        }

        card['attributes'] += attributes
        return card


    def send_card(self, card, color='gray', notify=False):
        params = {}
        params['from'] = self.from_name[:64]  # max length is 64
        params['message_format'] = 'html'
        params['color'] = color
        params['notify'] = notify
        params['message'] = '%s<br />%s' % (card['activity']['html'], card['description']['value'])
        params['card'] = card
        url = ('%s?auth_token=%s' % (self.msg_uri, self.token))

        try:
            response = open_url(url, data=json.dumps(params), headers={'Content-type': 'application/json'}, method="POST")
        except:
            self._display.warning('Could not submit message to hipchat %s' % (traceback.format_exc()))

    def v2_playbook_on_start(self, playbook):
        self.playbook_name = os.path.basename(playbook._file_name)
        self.playbook_path = "%s/%s" % (playbook._basedir, self.playbook_name)

        # strip repo-root from playbook_path
        repo_root = self.git_root_path(playbook._basedir)
        if repo_root:
            repo_root = os.path.dirname(repo_root)
            self.playbook_path = self.playbook_path[len(repo_root)+1:]

        notification = "Began execution of <strong>%s</strong>" % (self.playbook_name)
        if self.client_code is not "unknown":
            notification = "<strong>%s:%s</strong><br />%s" % (self.client_code, self.environment, notification)

        # send command line arguments as description / card body
        description = "%s %s" % (os.path.basename(sys.argv[0]), " ".join(sys.argv[1:]))

        card = self.make_card(notification, description, [{
            'label': "playbook",
            'value': {
                'label': self.playbook_path,
            }
        }])
        self.send_card(card)


    def playbook_on_stats(self, stats):
        hosts = sorted(stats.processed.keys())
        failures = False
        unreachable = False
        color = 'green'

        for h in hosts:
            s = stats.summarize(h)
            if s['failures'] > 0:
                failures = True
            if s['unreachable'] > 0:
                unreachable = True

        notification = "Completed execution of <strong>%s</strong>" % (self.playbook_name)
        if self.client_code is not "unknown":
            notification = "<strong>%s:%s</strong><br />%s" % (self.client_code, self.environment, notification)

        # send command line arguments and runtime as description / card body
        description = "%s %s" % (os.path.basename(sys.argv[0]), " ".join(sys.argv[1:]))

        if failures:
            color = 'yellow'
            notification += " (with <em>failures</em>)"

        card = self.make_card(notification, description, [{
            'label': "duration",
            'value': {
                'label': "%id %ih %im %is" % (self.days_hours_minutes_seconds(datetime.now() - self.start_time))
            }
        }])

        self.send_card(card,color)

    #
    # FAILURES
    #

    def v2_runner_on_failed(self, res, ignore_errors=False):
        if ignore_errors:
            return

        notification = "<strong>%s</strong> <em>failed</em> to execute: <br />%s" % (res._host, res._task)
        description = "%s" % (res._result['msg'])
        #self._display.warning(res._result)
        self.send_card(self.make_card(notification, description), color='red')


    def v2_runner_on_unreachable(self, res):
        notification = "<strong>%s</strong> <em>failed</em> on connection" % (res._host)
        description = "%s" % (res._result['msg'])
        self.send_card(self.make_card(notification, description), color='red')

    def v2_runner_on_async_failed(self, result):
        notification = "<strong>%s</strong> <em>failed</em> on async" % (res._host)
        description = "%s" % (res._result['msg'])
        self.send_card(self.make_card(notification, description), color='red')
