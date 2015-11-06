/**
 * exclude media -- because having webroot/media checked into git
 * breaks the symlink to the NFS media directory Rob so carefully
 * added to the 4 production webnodes
 */


var gulp = require('gulp');
var git  = require('git-promise');

gulp.task('exclude:media', function(cb){

  git('ls-files ' + MAGE_ROOT + '/media').then(function(stdout){
    if(stdout){
      return cb("webroot/media has checked in files. this is known to break NFS symlinks on the server");
    }
    cb();
  });
});

module.exports = {  
  all: 'exclude:media'
};
