MIS README
=========================

# Quickstart

### requirements
* [blueacorn bootstrap](https://github.com/BlueAcornInc/bootstrap)
  * This is only needed for the initial environment creation and deployment scripts, both of which are handled internally by Blue Acorn Systems Architects. If you are only developing code on the site, you do not need this file. 

```sh
# clone repository to your preferred working directory
git clone @GIT_REMOTE $BLUEACORN_PROJ_DIR/MIS

# change to cloned repository directory
cd $BLUEACORN_PROJ_DIR/MIS 

# set the current environment to 'local'
./env.sh set local

# configure .htaccess, app/etc/local.xml as per usual
#   NOTE: read more about environments in the env/ folder README
$EDITOR env/local/webroot/app/etc/local.xml
$EDITOR env/local/webroot/.htaccess
```

### Setup
* [Grunt and Git Hooks](https://docs.google.com/document/d/1jVSJvUvYxNvKu2Mcs5wtN00lVAeV53iG3sPBkJ9hCs8/)
  * This setup process is mandatory in order for the site to function correctly. Do not skip this step.



# Development Process

### "Rules of the Road"
* Branch from __develop__.
* Use [Pull Requests](https://help.github.com/articles/creating-a-pull-request/) to merge your branch back into __develop__. Do not `git merge`. 
  * THINK: you can't create a pull request if you work directly on __develop__!

* Your Pull Requests MUST be approved before they are merged into develop. Approvals are done internally by Blue Acorn.
* Do not make any changes to the Magento directly on the servers. The deployment scripts are designed to fail if there are any unstaged changes in order to prevent removing code.


##### Stale Branches

If your branch falls behind development, you may need to bring it up to date
before it can be **merged without conflict**. 

You have two options to update: __merge__ the `origin/develop` branch into your own branch, or __rebase__ your branch onto the `origin/develop` branch. Refer to 
[merging-vs-rebasing](https://www.atlassian.com/git/tutorials/merging-vs-rebasing/) for great information.

Generally:

1. if you have not merged other branches into your feature branch, use 
  * `git fetch origin && git rebase origin/develop`. it's cleanest.
2. if you have merged other branches into your feature branch, use 
  * `git fetch origin && git merge origin/develop`

Please keep in mind that rebasing a branch is a destructive process. Because of this, you should generally not rebase your branch if anyone else is using or working on it, or if you have previously merged in other branches.

When in doubt, it's always easier to merge `origin/develop` into your branch.

In both cases; use `git mergetool` for **resolving conflicts**. Set your mergetool beforehand (you can use any method you want to fix conflicts, but using a proper mergetool can save a lot of time).
  * [diffmerge](https://sourcegear.com/diffmerge/)
  * [meld](http://meldmerge.org/)


# Usage

## Utility

The `manage.sh` script provides shortcuts for common routines. Execute
it without arguments for help. 

Currently you can set the environment, print configuration variables, clear the
cache of an environment an environment, &c. It works in tandem with [environments](env/).

We want to continually improve this script and make the skel more helpful.
Please feel free to contribute your ideas

## Testing

Testing is performed by a javascript-based test suite. A docker container 
pre-installed with all test dependencies is provided for your convenience via
`launch.sh tests`. 

You may also run tests by executing `gulp` in the [tests/](tests/) directory. 
You'll need node and [gulp](http://gulpjs.com/) installed.

## Deployment

Deployment is typically performed by triggering the [MIS Jenkins Deployment Job](http://jenkins.badevops.com/job/MIS/).

Jenkins asks for the environment to deploy to and _optionally_ a git reference
to checkout. You can override the configured default by providing a git reference (e.g. a branch name, release tag, or commit hash). For instance, this allows you to __rollback__ a deployment of the -master branch to the previous release tag. 

Fear not. If Jenkins is down you can still deploy. The deployment job simply:

1. checks out the git reference
1. executes tests and prepares static assets (e.g. compiles CSS if configured)
1. if tests pass and asset preparation succeeds, runs the environment deployment playbook.


See the [environments README](env/README.md) to learn more.


# Release Process

Releases mark 'last known good' states from the -develop branch. 

We merge -develop into -master and tag this commit with a sortable timestamp so
we can easily rollback when necessary. 

From the GitHub repository homepage, go into 'Releases' and click 'Draft a New Release'. 

* Create a pull request to master and make sure it's approved
  * Base: master, compare: develop
* Use the output of this bash command as the tag for your release ```$(date "+%Y.%m.%d-%H.%M.%S")```
* Set target to master
* Set release title and description to whatever feels right
* Then Publish Release
