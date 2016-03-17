# devops-skel
the meta residing outside webroot

## goals

moving an application from the root of a repository to /webroot allows us to
keep additional data and tools. the skel represents these tools and data, and
is meant to provide:

* consistency (every project using skel shares the same api/concepts)
* environments (or "deployment targets") and tools for managing them.
* downstreaming (or the ability to keep skel consistent across all projects).


## development / bugs

* **known flaw** by keeping skel/ tracked in development branches we run the risk of confusing change behavior.

e.g. configuration files that get symlinked by `bin/env set <env>` are tracked
in the current development branch, which may not match what's running in the environment.

Imagine an update is made to the _production_ environment's local.xml and merged into _develop_ (the default gitflow branch). Because the production environment typically tracks the _master_ branch, production will not receive the updated local.xml until -develop is merged into -master -- which can be confusion.

  * immediate workaround - run `groundcontrol pull` after deployment and before
    `bin/env set` executes. ignore skel/ changes in dirty working copy check
  * long term workaround - we [plan to move skel/](https://github.com/BlueAcornInc/devops-skel/issues/51) to a submodule or orphan branch
    to match expect change behavior (by creating a single authority)


## directories


#### ansible

the `ansible/` directory contains playbooks for automated deployment,
provisioning, cache-clearing, snapshoting, and cron|utility setup.

the playbooks are usually called by the bin/ tools, for instance
`bin/deploy prod` will run the `ansible/prod.deploy.yml` playbook using
`env/prod/ssh_config` as hosts.

playbooks are copied from `boilerplate/ansible/` when an environment is created --
allowing for site/environment-specific customization.

#### bin

the `bin/` directory holds utility scripts and tools designed for launching
ansible playbooks, tests, dockerized sites, and skel management.


#### env

environments are defined in the `env/` directory.

an environment is a grouping of configuration files specifying where a site runs.
think of environments as "deployment targets" -- with an `ssh_config` defining
hosts, `envars` files defining the environment (such as APP_URL), and
configuration files  (such as local.xml and robots.txt) in `webroot`.

this allows us to keep environmental information and configuration in
tracked in version control, and provides a common representation used
by our ansible automation.

use `bin/env set <name>` to set an environment (which links its configuration
  on the current machine).

See [ENVIRONMENTS.md](ENVIRONMENTS.md) for more.


#### skel

the `skel/` directory containers boilerplate that is merged into the repository
root whenever `bin/skel init` runs.

keep authoritative files here (things you'd like to keep consistent across all
skel-attached projects). good canidates are common modules/libraries, tests,
global .gitignore, etc.

#### sync

executing `bin/env sync` copies the contents of the `sync` folder into
`webroot` --  allowing, for instance, to track files that are normally under
.gitignore.

common assets (such as WYSIWYG images in media/) and code may be added
to the `sync` directory.

**we run sync on every deployment, and whenever an environment is set.** e.g.
during `bin/deploy` and `bin/env set`

## usage

attach skel to a project using the attach-skel script. it's important to
use this script as it adds skel/ as a [git subtree](https://github.com/git/git/blob/master/contrib/subtree/git-subtree.txt),
allowing for easy downstreaming.

```
cd /path/to/project/repository/root
curl https://get.blueacorn.net/attach-skel.sh | sh
```

after attaching, be sure to initialize the skel via `bin/skel init --help`

use the `bin/env` and `bin/skel` tools for managing skel and environments.

once you have created environment(s), be sure to persist them to groundcontrol


## groundcontrol

Imagine the scenario where we deploy to production by running
`bin/deploy prod` from the _-master branch_. Now imagine that yesterday an
architect working from the _-develop branch_ added a new host to
`env/prod/ssh_config`. Lets pretend that _-develop_ has not yet been merged
into _-master_ (e.g. we're deploying a hotfix). The result? The new host is
excluded from the deployment, a dangerous and inconsistent state.

Groundcontrol exists to prevent this inconsistency. **Whenever changes are
made to the skel/, they are to be pushed to groundcontrol**. Do this via

```
bin/groundcontrol push
```

In addition to to providing an up-to-date skel/, groundcontrol will aggregate
skels across all projects into a single [repository](https://github.com/BlueAcornInc/devops-groundcontrol)
known as "outer-space".

Tools such as deploybot and automated snapshotting consume the skels in
outer-space... so it's super important to get your skel into groundcontrol and
keep it up to date!
