Draft MIS README
=========================

# Quickstart

```sh
# clone repository to your preferred working directory
git clone @GIT_REMOTE $BLUEACORN_PROJ_DIR/MIS

# change to cloned repository directory
cd $BLUEACORN_PROJ_DIR/MIS

# set the current environment to 'local'
./manage.sh env local

# configure .htaccess, app/etc/local.xml as per usual
#   NOTE: read more about environments in the env/ folder README
$EDITOR env/local/webroot/app/etc/local.xml
$EDITOR env/local/webroot/.htaccess
```


# Development Process

### "Rules of the Road"
* Branch from __develop__.
* Use [Pull Requests](https://help.github.com/articles/creating-a-pull-request/) to merge your branch back into __develop__. Do not `git merge`.
  * THINK: you can't create a pull request if you work directly on __develop__!

* Don't approve your own pull requests. Four eyes on all code.


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

## Environments

The `env.sh` script provides shortcuts for managing the current environment. Execute
 without arguments for help.

Before starting, you'll want to set the environmnet to `local`.

### Useful Environment Helpers
  * shell into an environment via `scripts/tmux.sh`
  * clear the cache of an environment via `scripts/cacheclear.sh`
  * deploy to an environment via `scripts/deploy.sh`

See the [docs/ENVIRONMENTS.md](docs/ENVIRONMENTS.md) to learn more.

## Testing

Testing is performed by a javascript-based test suite. For your convenience a docker container
pre-installed with all test dependencies can be launched via `scripts/tests.sh`.

You may also run tests by executing `gulp` in the [tests/](tests/) directory.
You'll need node and [gulp](http://gulpjs.com/) installed.

## Deployment

Deployment is typically performed by triggering the [MIS Jenkins Deployment Job](http://jenkins.badevops.com/job/MIS/) or hipchat.

These use the `scripts/deploy.sh` command.

See the [docs/DEPLOYMENTS.md](docs/DEPLOYMENTS.md) to learn about manually deploying.


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
