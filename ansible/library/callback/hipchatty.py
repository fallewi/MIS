# Make coding more python3-ish
from __future__ import (absolute_import, division, print_function)
__metaclass__ = type

from ansible.plugins.callback import CallbackBase

import os
import json
import re
import requests


class CallbackModule(CallbackBase):
    """
    hipchatty logs ansible and ansible-playbook runs to hipchat

    callback_plugins   = <path_to_callback_plugins_folder>
    and put the plugin in <path_to_callback_plugins_folder>

    This plugin makes use of the following environment variables:
    HIPCHAT_TOKEN   (optional): defaults to BlueAcorn Users
    HIPCHAT_ROOM    (optional): defaults to "deployments"
    HIPCHAT_HOST    (optional): defaults to "api.hipchat.com"
    """
    CALLBACK_VERSION = 2.0
    CALLBACK_TYPE = 'aggregate'
    CALLBACK_NAME = 'hipchatty'
    CALLBACK_NEEDS_WHITELIST = True

    def hipchat_notify(self, message, color='gray', notify=False,
      room=os.getenv('HIPCHAT_ROOM', 'deployments'),
      token=os.getenv('HIPCHAT_TOKEN', 'B3QQ016elG65pEIPFDhALCAJzTy1Edcoh6uTybJe'),
      host=os.getenv('HIPCHAT_HOST', 'api.hipchat.com')):

        payload = {
            'message': message,
            'notify': notify,
            'message_format': ('text' if re.search('<[a-zA-Z][\s\S]*>', message) is None else 'html'),
            'color': color
        }

        url = "https://%s/v2/room/%s/notification" % (host, room)
        headers = {'Content-type': 'application/json'}
        headers['Authorization'] = "Bearer %s" % (token)

        r = requests.post(url, data=json.dumps(payload), headers=headers)
        r.raise_for_status()


    def v2_playbook_on_play_start(self, play):
        self.hipchat_notify(repr(play))

    def v2_playbook_on_task_start(self, task, is_conditional):
        self.hipchat_notify(repr(task))
