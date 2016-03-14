environments
============

environments are a grouping of configurations, host definitions, and automation
tasks that define where a site runs.  think of environments as
"deployment targets" including
  * host definitions in ssh_config format
  * variable definitions in shell var format (such as APP_URL and CLIENT_CODE)
  * versioned configuration files (such as local.xml, robots.txt)
  * ansible playbooks used for automated tasks (such as cache clearing and deployment)

_architects are responsible for definining and maintaining environments,
  although their transparent nature allows anyone to contribute._

a typical repository has a "local" environment representing the
developer machine, a "dev" environment representing machines
running the -develop branch, and a "prod" environment representing
machines running the -master branch.


environments are managed via `bin/env`, and _must be set_ (e.g. after a skel
codebase is cloned).

executing `bin/env set local` activates the "local" environment by creating
a symlink named `skel/env/current` and pointing it to `skel/env/local`.


## management

use `bin/env` to manage environments. `bin/env make` is used to create a new
environment.

when creating a new environment, you choose what to base it on. choose
the boilerplate example which closely resembles the new environment --or--
you may base it off an already created environment.

In addition to creating your environment under `skel/<env>`, environment-specific
ansible playbooks are added to `skel/ansible/<env>.<playbook>.yml`. These must
be configured as well. See [ANSIBLE.md](ANSIBLE.md) for more about ansible.



## configuration

each environment has a `webroot/` directory with configuration files such as
local.xml, robots.txt, &c.

_because local.xml is typically in .gitignore, this pattern allows us to track configuration files_.

activating an environment (`bin/env set <env>`) will symbolically link files
under skel/env/<env>/webroot to their corresponding location in APP_ROOT

#### ssh_config
each environment has a __ssh_config__ file describing the hosts making it up
in [ssh_config format](http://www.tldp.org/LDP/solrhe/Securing-Optimizing-Linux-RH-Edition-v1.3/chap15sec121.html)

this file is utilized by tools (like `bin/tmux` and `bin/develop`) that connect
 to hosts in an environment.

#### application and environment variables

shellvar files provide a consistent source of information about a site.

the **global** appvars file (`skel/env/appvars`) is extended by
an environment-specfic envars file (`skel/env/prod/envars`).

envars definitions clobber appvars definitions.

##### required variables

variable | description | example
-------- | ----------- | -------
CLIENT_CODE | client code | SIG
REPO_REMOTE | repository git remote | git@github.com:BlueAcornInc/SIG.git
BLUEACORN_DIR | ABSOLUTE path to blueacorn directory | /var/blueacorn
REPO_ROOT | path to repository on server | /var/blueacorn/sites/www.signaturehardware.com
REPO_REF | default reference/branch | master
DOMAIN | base domain | signaturehardware.com
SUBDOMAIN | environment subdomain | www
APP_ROOT | absolute path to application webroot | ${REPO_ROOT}/webroot | -
APP_URL | default store URL | http://${SUBDOMAIN}.${DOMAIN}/


##### optional, provisioning/docker variables

variable | description | example
-------- | ----------- | -------
APP_DB_HOST | mysql hostname or IP | 255.255.255.255
APP_DB_NAME | mysql database name | site_prod
APP_DB_USER | mysql username | blueacorn
APP_DB_PASS | mysql password | insecure
MAGE_ADMIN_USERNAME | admin username | admin
MAGE_ADMIN_PASSWORD | admin password | insecure
MAGE_ADMIN_FIRSTNAME | admin first name | Blue
MAGE_ADMIN_LASTNAME | admin last name | Acorn
MAGE_ADMIN_EMAIL | admin email | noreply@blueacorn.com


#### tmuxp.yml


the tmuxp.yml defines what to do when `bin/tmux` is executed.

it is a simple [tmuxp](https://github.com/tony/tmuxp) script, that
usually connects to all hosts in ssh_config and changes into the APP_ROOT
directory on web nodes.

See [TMUX.md](TMUX.md) for more.
