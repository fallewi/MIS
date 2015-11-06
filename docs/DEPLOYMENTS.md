## Deployment

**Manual** deployment instructions.

Automatic deployments are performed by triggering the [MIS Jenkins Deployment Job](http://jenkins.badevops.com/job/MIS/).


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
