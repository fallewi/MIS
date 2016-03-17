Deployments
===========

Skel projects leverage "git based" deployments -- meaning git is used
to set the codebase on remote webservers to a desired version (git ref).

Deployments are typically triggered by the [MIS Jenkins Deployment Job](http://jenkins.badevops.com/job/MIS/), which runs an
ansible deployment via `bin/deploy` against a specified environment and git ref.


### Ansible Deployment

A per-environment playbook exists to perform deployments which:
  * executes pre-deployment tasks (such as asset compilation)
  * manages dependencies
  * performs the deployment
  * executes post-deployment tasks (such as cache clearing and notifications).


Use `bin/deploy` to deploy, and understand:

* ansible connects to servers using ssh and your private key.
  * you may pass the password and sudo-password `--ask-pass` and `--sudo-pass` flags.
* ansible uses the `github-deploy.key.pub` key when interfacing with repositories. ensure it
   has been added as a [deploy key]((https://github.com/blog/2024-read-only-deploy-keys) to this repository.

##### bin/deploy basics

Keep in mind that deploy is an environment-specific command, and the hosts that get deployed to are in the environment's
ssh_config. 

When executing `bin/deploy <env>`, the `skel/ansible/<env>.deploy.yml` ansible playbook runs. Playbooks run against
a set of hosts -- and these hosts are defined in `skel/env/<env>/ssh_config`. 

The ssh_config must be kept up to date. See [ENVIRONMENTS.md](ENVIRONMENTS.md) for more on environments.

Architects are tasked managing this playbook per environment and
sending changes to [groundcontrol](docs/SKEL.md#groundcontrol) -- although
its transparent nature allows contributions from anyone.



### Manual Deployment


#### Terms
Term | Description
---- | -----------
Deployment Host | Machine performing the deployment (e.g. developer computer or jenkins host)
Deployment Targets | Web nodes. Typically defined by environment (e.g. web-1, web-2, web-admin)
Deployment Ref | Git reference (e.g. release tag, branch name, or commit hash) to deploy. Default ref is defined by environment. Typically used to __rollback__ a deployment.
Assets | Static Assets (javascript, css) compiled on the deployment host and copied to targets.

#### Prerequsites

**Manual** provisioning. Automatic provisioning may be handled by ansible scripts.

These steps are run _once_.

##### Deployment Host Requirements
 * grunt
 * bower
 * git
 * rsync

assumes repository is checked out in __/sites/MIS__
```sh
cd /sites && git clone git@github.com:BlueAcornInc/MIS.git
```

if blueacornui exists, configure for compiling assets
```sh
cd /sites/MIS/blueacornui
npm install
bower install
```

##### Deployment Targets Requirements
 * git
 * rsync

assumes repository is checked out in __/sites/MIS__ , and default environment branch checked out.
```sh
cd /sites && git clone git@github.com:BlueAcornInc/MIS.git
cd /sites/MIS
git checkout develop # or master or whatever environment default.
```

assumes httpd is setup with __server root__ pointing to __webroot__ directorty
```
# nginx example
###############
server {
  listen       80;
  server_name  www.missionrs.com;
  root         /sites/MIS/webroot;
  ...

# apache example
################
<VirtualHost *:80>
	ServerName www.missionrs.com
	DocumentRoot /sites/MIS/webroot
	...
```

#### Deployment Process

1. (if blueacornui exists) On __Deployment Host__, compile Assets in "production" mode
  ```
  cd /sites/MIS/blueacornui
  grunt production

  # assets are compiled to:
  #  /sites/MIS/webroot/skin/frontend/blueacorn/site/css/
  #  /sites/MIS/webroot/skin/frontend/blueacorn/site/jsmin/
  ```

1. On __Deployment Targets__, checkout desired Deployment Ref'
  ```
  cd /sites/MIS
  git fetch && git reset <DEPLOYMENT_REF> --merge
  ```

1. (if blueacornui exists) On __Deployment Host__, copy Assets to Deployment Targets
  ```
  cd /sites/MIS/webroot/skin/frontend/blueacorn/site
  rsync -zrL --delete-after css/ target_host:/MIS/webroot/skin/frontend/blueacorn/site
  rsync -zrL --delete-after jsmin/ target_host:/MIS/webroot/skin/frontend/blueacorn/site
  ```
1. On __Deployment Target(s)__, Clear the FPC and Object Cache
  * use n98-magerun -or-
  * use redis-cli (if redis)
  * or clear directories using filesystem (e.g. `rm -rf var/{cache,full_page_cache}`)


See the [environments README](docs/ENVIRONMENTS.md) to learn more.
