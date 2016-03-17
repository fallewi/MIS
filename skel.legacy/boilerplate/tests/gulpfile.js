/**
 * blueacorn test runner.
 *
 *   gulp/node based for accessible parallel execution of tasks
 *
 *   tests are organized under tests.d/ directory, matching *.js
 *
 *   EXIT CODES:
 *     0 if no errors or warning
 *     1 if errors
 *     6 if warnings
 */

var gulp  = require('gulp');
var gutil = require('gulp-util');

global.REPO_ROOT = require("path").resolve(__dirname + '/../');
global.MAGE_ROOT = REPO_ROOT + '/webroot';

// error|warning|info handling ....
///////////////////////////////////

var failOnWarnings = false;

gulp.warnings = {
  count: 0,
  log: function(message) {
    this.count++;
    if(failOnWarnings) {
      gutil.log(gutil.colors.red.bold('===ERROR==='));
      gutil.log(gutil.colors.red(message));
    }
    else {
      gutil.log(gutil.colors.yellow.bold('===WARNING==='));
      gutil.log(gutil.colors.yellow(message));
    }
  }
}

process.on('exit', function(){
  var exitCode = (gulp.warnings.count > 0) ? 6 : 0; 

  if(exitCode && failOnWarnings) {
    exitCode = 1;
  }
  process.exit(exitCode);
});


// https://www.npmjs.com/package/gulp-dir
//   load all *.js files in /tests.d
require('gulp-dir')(__dirname + '/tests.d');

gulp.task('default',['all']);

