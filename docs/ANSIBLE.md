ansible automation
==================

the `skel/ansible` contains [ansible playbooks](http://docs.ansible.com/ansible/playbooks.html)
for performing routine tasks against environments.


### configuring

* when an environment is created, _boilerplate_ playbooks from the
`skel/boilerplate/ansible/` directory are copied into the ansible directory and
prefixed with the environment name. each playbook performs a specific task,
and is to be adjusted to match the environment.
  * after downstreaming skel, double check the boilerplate playbooks for reference changes

* included tasks in the `skel/ansible/tasks/` directory should **not be modified**.
these are controlled by the skel, and if you need to change behavior it is best
to copy their contents into the boilerplate (environment) playbook and remove
the include.

* in addition to appvars and envars, ansible reads [inventory variables](http://docs.ansible.com/ansible/intro_inventory.html#splitting-out-host-and-group-specific-data)
from the `skel/ansible/group_vars` and `skel/ansible/host_vars` folders. use
these files to adjust per-environment playbook behavior.

* during deployments, ansible pulls the latest changes from the remote machine using git, ensure the
  `github-deploy.key.pub` has been added as a [deploy key]((https://github.com/blog/2024-read-only-deploy-keys) to this repository.

* our [ssh_hosts dynamic inventory script](../ansible/ssh_hosts.py) parses the environment's `ssh_config` file and `envars` to create an ansible
inventoy. be sure these are configured for the environment.


### requirements

* [ansible 2.0+](http://www.ansible.com/)

### usage

many of the `bin` commands are wrappers to these playbooks -- e.g.
`bin/deploy prod` runs the `skel/ansible/prod.deploy.yml` playbook,
passing in hosts defined by `skel/env/prod/ssh_config`.


* ansible connects to servers with ssh using your private key.
  * you can pass the private key (via `--private-key`) to use, or a password (via `--ask-pass`) if your key is not yet present on the target host authorized_keys file.
* playbooks generally expect the remote user to have passwordless sudo access
  * you may pass the sudo-password to use via `--sudo-pass`.


#### examples

##### flush cache in "dev" environment
```sh
bin/util cacheclear dev
```

##### perform a MEDIA-ONLY snapshot of "prod"
```sh
bin/snapshot prod --tags media
```

##### deploy/rollback "prod" to a tag/branch
```sh
# assumes previous release tag of: 2015.09.02-01.05.50
bin/deploy prod --extra-vars="GIT_REF=2015.09.02-01.05.50"
```


##### preview provisioning changes to web-1 in the "dev" environment
```sh
bin/deploy dev --dry-run --limit web-1
```
