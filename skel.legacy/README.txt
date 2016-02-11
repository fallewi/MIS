#@ TODO: document webroot, new skel org, envfiles

start spot for new client sites based on our pilot best practices.

the place for consistency across sites -- modules and scripts we _all_ use.

---

[terms]
environment : a grouping of configuration files defining where a site runs.
              think of environments as "deployment targets" defining the hosts
              (ssh_config), configuration files (local.xml), and playbooks
              (ansible) used for cache clearing and deploymnent.

              a typical repository has a "local" environment representing the
              developer machine, a "dev" environment representing machines
              running the -develop branch, and a "prod" environment representing
              machines running the -master branch.

[files]
/env.sh     : environment management utility
              set, make, list, and print environment variables

/manage.sh  : repository management utility
              initialize the codebase and envars, pilot updates, &c.

[directories]
/ansible/   : per-environment playbooks for automated deployment, provisioning,
              cache-clearing, and cron|utility setup.

/env/       : environment definitions and configuration files

/tests/     : functional tests. add site-specific tests to tests.d/

/webroot/   : files and modules we want to keep consistent across all sites

---

NOTE:

we continuously apply this repository to maintain consistency across pilots.

  * if a file is to be kept consistent across all sites, add it to the
    /.gitignore.skel -- otherwise it will be considered "boilerplate" and
    overridable.
