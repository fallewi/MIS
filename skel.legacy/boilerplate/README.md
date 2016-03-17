Draft @CLIENT_CODE README
=========================

# Usage

This is a [magento-skel](skel/README.md) based project.

## Environments

An environment is a grouping of configurations, host definitions, and automation
tasks stored under a directory in `skel/env/`. The skel interacts with these
environments using a _common set of tools_ under `bin/`.

Use `bin/env` to manage environments.

Before starting, you'll want to set the environment to `local`. Setting an
environment links configuration (e.g. app/etc/local.xml) and should be run
at least once per installation.

```
cd /path/to/repo
bin/env set local

# configure .htaccess, app/etc/local.xml as per usual
$EDITOR env/local/webroot/app/etc/local.xml
$EDITOR env/local/webroot/.htaccess
```

### Useful Environment Tools
  * clear cache of an environment via `bin/util cacheclear`
  * shell into environment hosts via `bin/tmux`
  * deploy to an environment via `bin/deploy`
  * snapshot an environment via `bin/snapshot`

See the [docs/ENVIRONMENTS.md](docs/ENVIRONMENTS.md) to learn more.


## syncing .gitignored files

To track assets in .gitignored directories (e.g.  media/ and var/), add them
to the `skel/sync/` directory.
  * e.g. track WYSIWYG images by placing them in `skel/sync/media/`

The `bin/env sync` command copies these assets into the base `webroot`, and
automatically runs whenever an environment is set or deployment made.


# Development Process

We use a "gitflow" process where -master represents a snapshot of
the -develop branch where work is based. The -master branch runs on
"production" and can be considered a stable point-in-time of -develop. Thus
-develop represents the future of -master. Whenever a release is ready, we
merge -develop into -master.


### "Rules of the Road"
* Organize your work in limited-scope feature branches and breakup your commits
  into small thematically related changes with a representative message.
  * Branch from __develop__ or the branch defined by your architect.
* Use [Pull Requests](https://help.github.com/articles/creating-a-pull-request/) to merge your work into the branch denoted by your architect -- usually an integration branch or -develop.  Do not `git merge`.
  * GitHub provides a web interface for creating Pull Requests. You can also use `hub` CLI.
  * Don't approve your own pull requests. Four eyes on all code.
* __Never force push__ if your work is shared.
  * commands like `git rebase` rewrite history. git uses history (`merge-base`)
  for optimizing merging. if someone someone branched off your codebase, and
  you rewrite history, the common commit can be lost -- resulting in merges
  that have 1000s of file changes, when only a handful changed.


#### Stale Branches

If your branch falls behind development, you may need to bring it up to date
before it can be **merged without conflict**.

You have two options to update: __merge__ the upstream develop branch into your own branch, or __rebase__ your branch against upstream develop. Refer to
[merging-vs-rebasing](https://www.atlassian.com/git/tutorials/merging-vs-rebasing/) for great information.

Generally:

1. *rebase* if your branch can be merged without conflict, and you are the __only person working on or from this branch__.
  * rebase is destructive -- see note re: "Never force push"
  * `git fetch upstream && git rebase upstream/develop`
1. *merge* if anyone else is working on your branch, or if you have merged in other branches.
  * `git fetch upstream && git merge upstream/develop`


Personally I rebase whenever I forget to pull in changes before commiting. This
avoids the additional merge commit.

#### Merge Conflicts

Conflicts happen when git is unable to determine what changes to
preserve -- usually when two developers are working on the same file, and in
particular the same parts of a file.


Use `git mergetool` for **resolving conflicts**. Set your mergetool beforehand (you can use any method you want to fix conflicts, but using a proper mergetool can save a lot of time).
  * [diffmerge](https://sourcegear.com/diffmerge/)
  * [meld](http://meldmerge.org/)


If you don't know what changes to preserve, ask the original developer(s) to
perform the merge. You can find the author of a line of code using `git blame`.


## Testing

Testing is performed by a javascript-based test suite. For your convenience a docker container
pre-installed with all test dependencies can be launched via `bin/tests`.

You may also run tests by executing `gulp` in the [tests/](tests/) directory
(you'll need node and [gulp](http://gulpjs.com/) installed for this).

```sh
# run tests in docker [prefered]
cd /path/to/repo
bin/tests

# alternatively, manually run tests [requires node + gulp]
cd /path/to/repo/tests
npm install
gulp
```

**Add tests for your feature or hotfixes** to tests/tests.d . See [docs/TESTS.md](docs/TESTS.md) for more.



# Release Process

Releases mark 'last known good' states from the -develop branch.

We merge -develop into -master and tag this commit with a sortable timestamp so
(so we can rollback to a previous release if necessary).

From the GitHub repository homepage, go into 'Releases' and click 'Draft a New Release'.

* Create a pull request to master and make sure it's approved
  * Base: master, compare: develop
* Use the output of this bash command as the tag for your release ```$(date "+%Y.%m.%d-%H.%M.%S")```
* Set target to master
* Set release title and description to whatever feels right

## Hotfixes

When we need to ship code that is not part of a release, a hotfix is to be
created. In this special case, base your hotfix branch off -master. When it
is finished, merge it back into -master AND -develop.

* base hotfix branch off -master, named `hotfix/desrcription_of_fix`
  ```
  git checkout master
  git checkout -b hotfix/calm_explosions
  ```

* when your fix is ready and tested merge into -develop and -master
  ```
  git checkout master
  git merge --no-ff hotfix/calm_explosions
  git checkout develop
  git merge --no-ff hotfix/calm_explosions
  ```

The point is that hotfixes must be applied to _develop as well as master_.


## Deployment

Deployments are to be scheduled for each release. Contact architect for details.

Deployments are typically triggered by the [@CLIENT_CODE Jenkins Deployment Job](http://jenkins.badevops.com/job/@CLIENT_CODE/) or hipchat,
but can also be performed via the CLI. Each uses uses `bin/deploy` Environment
script to checkout a git reference on defined web hosts, clear cache, etc.

See the [docs/DEPLOYMENTS.md](docs/DEPLOYMENTS.md) to learn more.

# Special Notes

* Architect: Jane Doe
* Deployment Window: TBD
* WIKI: [@CLIENT_CODE wiki](https://www.blueacorn.com/)
