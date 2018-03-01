MissionRS Deployments
===========
In this document, we will outline the steps to perform a deployment and the branching stategies.

## Branching

The following table outlines the branch merging process we use to manage projects.

Branch | environment | Description
-------|-------------|------------
develop | none | develop represents the approved code that is production-ready, we typically branch from develop when building features. Ideally, develop and master are similar so when branching from develop for feature work, you should only have your commits and any commits that are slated for production when you merge that feature into master
unstable/release_x | staging | unstable branches, commonly known as releases, represent non-approved in-progress work. We typically branch these from develop (sometimes we branch form master on projects where develop has commits we do not want to appear on production but this is technically against gitflow) and these are the branches a developer would open a PR into to deliever a feature branch.
deploy/release_x   | staging or preprod | always branched from master, with the unstable branched merged in. This step gives the deployer the opportunity to fix any merge conflicts that would occur when merging into master, and to deploy this to staging or preprod as a final check. What is in this branch will merge cleanly into master and can be considered a version of the master branch in it's deployed state. This is a very useful convention with larger projects, projects with light work might dispense of this step. 
master | production  | master represents the code as it exists on production, once a deploy branch is ready, it's merged into master and deployed.
integration/victor_development | staging/none | This is a branch Victor maintains with his code, when he PRs into this branch, we accept it, and then merge this branch into our unstable/release branch and deploy that branch to staging. Since this branch is based from master, we can optionally merge into master and deploy only his code to production. Typically, before the merge into master we merge the release into develop. It's also valid to merge into master, then merge master into develop and the integration/victor_unstable so these branches have the latest version of the code.




### Manual Deployment
#### Terms
Term | Description
---- | -----------
Deployment Host | Machine performing the deployment (e.g. developer computer or jenkins host)
Deployment Targets | Web nodes. Typically defined by environment (e.g. web-1, web-2, web-admin)
Deployment Ref | Git reference (e.g. release tag, branch name, or commit hash) to deploy. Default ref is defined by environment. Typically used to __rollback__ a deployment.
Assets | Static Assets (javascript, css) compiled on the deployment host and copied to targets.

#### Prerequsites

**Manual** provisioning.

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
  #  /sites/MIS/webroot/skin/frontend/blueacorn/mis/css/
  #  /sites/MIS/webroot/skin/frontend/blueacorn/mis/jsmin/
  ```

1. On __Deployment Targets__, pull the latest code from the branch
  ```
  cd /sites/MIS
  git fetch && git pull origin <branch>
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
