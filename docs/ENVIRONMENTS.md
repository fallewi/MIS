environments
============

environments are a grouping of configuration files defining where a site runs.


think of environments as "deployment targets" defining the hosts (ssh_config),
configuration files (local.xml), and playbooks (ansible) used for automated
tasks like cache clearing and deployment.


a typical repository has a "local" environment representing the
developer machine, a "dev" environment representing machines
running the -develop branch, and a "prod" environment representing
machines running the -master branch.


when an an environment is set via `env.sh`, a folder named `current` is symbolically linked to it. e.g. running `env.sh set local`
will activate the "local" environment, and `/env/current` will point to `/env/local`

## configuration


#### ssh_config
each environment has a __ssh_config__ file describing the hosts making it up
in [ssh_config format](http://www.tldp.org/LDP/solrhe/Securing-Optimizing-Linux-RH-Edition-v1.3/chap15sec121.html)

this file is utilized by tools to connect with the hosts in an environment.

#### envars

__envars__ files provide a consistent source of information about a site in
shell format. the **common envars file** in /env/ can be extended per-environment
by adding an envars file; e.g. `/env/production/envars` extends `/env/envars`


variable | description | example | scope
-------- | ----------- | ------- | -----
CLIENT_CODE | client code | SIG | global
REPO_REMOTE | repository url | git@github.com:BlueAcornInc/SIG.git | global, env
REPO_ROOT | path to repository on server | /sites/www.signaturehardware.com | env
REPO_REF | default reference/branch | master | env
MAGE_ROOT | absolute path to magento's webroot | ${REPO_ROOT}/webroot | env
MAGE_DB_HOST | mysql hostname or IP | 255.255.255.255 | env
MAGE_DB_NAME | mysql database name | site_prod | env
MAGE_DB_USER | mysql username | blueacorn | env
MAGE_DB_PASS | mysql password | insecure | env
MAGE_URL | URL of site | http://site.com/ | env**
MAGE_ADMIN_USERNAME | admin username | admin | env**
MAGE_ADMIN_PASSWORD | admin password | insecure | env**
MAGE_ADMIN_FIRSTNAME | admin first name | Blue | env**
MAGE_ADMIN_LASTNAME | admin last name | Acorn | env**
MAGE_ADMIN_EMAIL | admin email | noreply@blueacorn.com | env**

`env** optional vars (used by n98 isntaller for local | jenkins)`


#### webroot

the  __/webroot__ directories contain configuration files (local.xml, robots.txt, &c) that
are symbolically linked to the site's DocumentRoot when `manage.sh env <env>` is
executed.

this pattern allows us to maintain configuration files in version control.


## ansible automation

the __/ansible__ directories contain a collection of utility scripts to perform
routine tasks against an environment.  

#### requirements

* [ansible 1.9+](http://www.ansible.com/)
* [bootstrap](https://github.com/BlueAcornInc/bootstrap) - provides ssh_config inventory script


* ansible connects to servers using ssh and your private key.
  * ensure [jenkins user public key](https://files.badevops.com/ssh-keys/jenkins.key.pub) can connect to servers for deployment

* ansible pulls the latest changes from the remote machine using git, ensure the
  `github-deploy.key.pub` has been added as a [deploy key]((https://github.com/blog/2024-read-only-deploy-keys) to this repository.


#### usage

* utilizes the ssh_config file via the [ssh_hosts.py](https://files.badevops.com/ansible/ssh_hosts.py) [dynamic inventory](http://docs.ansible.com/ansible/developing_inventory.html) script
* interact with ansible through site.sh helper


#### examples

##### flush cache in "dev" environment
```sh
cd env
./site.sh clearcache dev
```

##### deploy to "prod" environment (to dev.site.com)
```sh
cd env
./site.sh deploy prod
```


##### deploy/rollback "prod" to a tag/branch
```sh
cd env
# assumes previous release tag of: 2015.09.02-01.05.50
./site.sh deploy --extra-vars="GIT_REF=2015.09.02-01.05.50"
```

## tmux

use `tmuxp` configurations to quickly shell into client servers.


#### requires

* tmux
* [bootstrap](https://github.com/BlueAcornInc/bootstrap) - provides [tmuxp](https://github.com/tony/tmuxp)

##### useful keys

prefix + key  | description
------------- | -------------
d  | detach (closes tmux). use `tmux` to re-attach later
o  | switch pane
z  | zoom pane (toggles between fullscreen and layout view)
PgUp | enter scroll mode (to scroll back). Esc exits.
`:setw synchronize-panes`  | toggles synchronization between panes. Use tab completion, e.g. "`<ctrl-b> :setw syn<tab>`"


* default tmux control character is __<ctrl>-b__
   * e.g. pressing __ctrl-b__, then "d" will detach you from the tmux session

#### usage

start tmux, shelling into "prod" environment hosts
```sh
cd env
./site.sh tmux prod
```

* kill all tmux sessions with `tmux kill-session`, kill specific session with `tmux kill-session SESSION_ID`, list sessions with `tmux ls`
