var gulp = require('gulp');
var git  = require('git-promise');

var protected_files = [
  'webroot/index.php',
  'tests/tests.d/watchlist.js'
];

gulp.task('watchlist:files', function(cb){

  // did webroot/index.php get updated?
  //   then SMS bobby, send a smoke signal, and shoot a rocket to mars

  var changes = [];

  protected_files.forEach(function(file){
    git('status --porcelain ' + file,{cwd: REPO_ROOT}).then(function(stdout){

      if(stdout){
        changes.push(stdout);
      }

      protected_files = protected_files.filter(function(i){
        return i != file;
      });

      // is this the last file processed?
      if(! protected_files.length) {

        if(changes.length) {
          gulp.warnings.log("protected files have changes ==>\n"
            + changes.join("\n"));
        }

        cb();
      }
    });
  });
});

gulp.task('watchlist:skel', function(){
  // TBD, overlay skel and look for changes.
  // perhaps use git list files on skel to update protected_files ???
});

gulp.task('watchlist', ['watchlist:files','watchlist:skel']);

module.exports = {  
  all: 'watchlist'
};
