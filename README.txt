start spot for new client sites based on our pilot best practices.

aka the place to add global functionality -- things we _all_ use.

---

[files]
/manage.sh  : utility for environment configuration and repository management
              symlinks conf files, configures git, initializes repo, &c.

/launch.sh  : spawns docker containers to perform a task. requires docker 1.6+
              e.g. start website on :9999 or run the same tests jenkins does.

[directories]
/.skel/     : boilerplate copied during `manage.sh init`. files within are
              useful as _reference_, and typically modified site to site.

/env/      : configuration files and utility scripts organized by environment

/tests/    : functional tests executed by jenkins. add site-specific tests
              to tests.d/ file, following the [nodejs, gulp] example pattern.

/webroot/  : files we want to keep consistent across all magento sites 

---

NOTE:

we continuously apply this repository to maintain consistency across pilots. 

  * if a file is to be kept consistent across all sites, add it to the 
    appropriate directory _outside_ of /defaults (e.g. /webroot/.gitignore)

  * keeping the bulk under the defaults/ directory prevents us from clobbering 
    site-specific changes. 

