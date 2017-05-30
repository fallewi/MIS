/**
* @package     BlueAcorn/GreenPistachio
* @version     4.5.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

'use strict';

var _ = require('underscore'),
    theme = require('./themes'),
    modules = require('./modules'),
    path = require('./path');

module.exports = {
    autopath: function(themeName, folder) {
        return path.webroot + folder + '/' +
            theme[themeName].area + '/' +
            theme[themeName].name + '/';
    },
    autopathModule: function(modExports, folder) {
        return path.webroot + folder + '/' +
            modExports.area + '/' +
            modExports.name + '/';
    },
    cssFiles: function(themeName) {
        var cssStringArray = [],
            i = 0,
            v = theme[themeName].files.length;

        for (i; i < theme[themeName].files.length; i++) {
            cssStringArray[i] = this.autopath(themeName, 'skin') +
                'css/' + theme[themeName].files[i] + '.css';
        }

        return cssStringArray;
    },
    scssFiles: function(themeName) {
        var self = this,
            scssStringArray = [],
            cssStringArray = [],
            scssFiles = {},
            i = 0;

        for(i; i < theme[themeName].files.length; i++) {
            cssStringArray[i] = this.autopath(themeName, 'skin') +
                'css/' + theme[themeName].files[i] + '.css';

            scssStringArray[i] = this.autopath(themeName, 'skin') +
                'scss/' + theme[themeName].files[i] + '.scss';

            scssFiles[cssStringArray[i]] = scssStringArray[i];
        }

        _.each(modules, function(modExports, idx){
            if(modExports.grunt && modExports.files.length > 0) {
                for(var m = 0; m < modExports.files.length; m++) {
                    cssStringArray[i]= self.autopath(themeName, 'skin') + 'css/' + modExports.files[m] + '.css';

                    scssStringArray[i] = self.autopathModule(modExports, 'skin') + 'scss/' + modExports.files[m] + '.scss';

                    scssFiles[cssStringArray[i]] = scssStringArray[i];

                    i++;

                }
            }
        });

        return scssFiles;
    },
    jsFiles: function(themeName) {
        if(theme[themeName].jsdirs.length > 0) {
            var sourceStringArray = [],
                minStringArray = [],
                jsFiles = {},
                i = 0;

                for(i; i < theme[themeName].jsdirs.length; i++) {
                    var subName = '';
                    if(theme[themeName].jsdirs[i] !== 'blueacorn' || theme[themeName].jsdirs[i].indexOf('blueacorn') === -1) {
                        subName = '.' + theme[themeName].jsdirs[i];
                    }
                    if(theme[themeName].jsdirs[i].indexOf('blueacorn') != -1) {
                        subName = theme[themeName].jsdirs[i].replace('blueacorn','');
                    }

                    minStringArray[i] = this.autopath(themeName, 'skin') + 'jsmin/blueacorn' + subName + '.min.js';

                    sourceStringArray[i] = this.autopath(themeName, 'skin') + 'js/build/' + theme[themeName].jsdirs[i] + '/**/*.js';

                    jsFiles[minStringArray[i]] = sourceStringArray[i];
                }
                return jsFiles;
        }
    },
    themeFallback: function(themeName) {
        var self = this,
            themeFallbackIncludes = [];
            themeFallbackIncludes.push(this.autopath(themeName, 'skin') + 'scss/');

        _.each(theme[themeName].bower_fallback, function(bower_component){
            themeFallbackIncludes.push(bower_component);
        });

        _.each(theme[themeName].theme_fallback, function(theme_fallback){
            themeFallbackIncludes.push(self.autopath(theme_fallback, 'skin') + 'scss/');
        });

        return themeFallbackIncludes;
    },
    themeFallbackSass: function(themeName) {
        var self = this,
            themeFallbackIncludes = [];
            themeFallbackIncludes.push(this.autopath(themeName, 'skin') + 'scss/**/*.scss');

            _.each(theme[themeName].theme_fallback, function(theme_fallback){
                themeFallbackIncludes.push(self.autopath(theme_fallback, 'skin') + 'scss/**/*.scss');
            });

            _.each(modules, function(modExports, idx){
                if(modExports.grunt && modExports.files.length > 0) {
                    for(var m = 0; m < modExports.files.length; m++) {
                        themeFallbackIncludes.push(self.autopathModule(modExports, 'skin') + 'scss/' + modExports.files[m] + '.scss');
                    }
                }
            });

        return themeFallbackIncludes;
    },
    themeFallbackJs: function(themeName) {
        var self = this,
            themeFallbackIncludes = [];
            themeFallbackIncludes.push(this.autopath(themeName, 'skin') + 'js/**/*.js');

            _.each(theme[themeName].theme_fallback, function(theme_fallback){
                themeFallbackIncludes.push(self.autopath(theme_fallback, 'skin') + 'js/**/*.js');
            });

            _.each(modules, function(modExports, idx){
                if(modExports.grunt && modExports.jsdirs.length > 0) {
                    themeFallbackIncludes.push(self.autopathModule(modExports, 'skin') + 'js/**/*.js');
                }
            });

        return themeFallbackIncludes;
    },
    themeFallbackApp: function(themeName) {
        var self = this,
            themeFallbackIncludes = [];
            themeFallbackIncludes.push(this.autopath(themeName, 'app/design') + 'layout/**/*.xml');
            themeFallbackIncludes.push(this.autopath(themeName, 'app/design') + 'template/**/*.phtml');

            _.each(theme[themeName].theme_fallback, function(theme_fallback){
                themeFallbackIncludes.push(self.autopath(theme_fallback, 'app/design') + 'layout/**/*.xml');
                themeFallbackIncludes.push(self.autopath(theme_fallback, 'app/design') + 'template/**/*.phtml');
            });

            return themeFallbackIncludes;
    },
    jsMinFiles: function(themeName) {
        if(theme[themeName].jsdirs.length > 0 && theme[themeName].grunt) {
            var minStringArray = [],
                i = 0;

                for(i; i < theme[themeName].jsdirs.length; i++) {
                    var subName = '';
                    if(theme[themeName].jsdirs[i] !== 'blueacorn') {
                        subName = '.' + theme[themeName].jsdirs[i];
                    }

                    minStringArray[i] = this.autopath(themeName, 'skin') + 'jsmin/blueacorn' + subName + '.min.js';
                }
                return minStringArray;
        }
    },
    jsFilesCopy: function(themeName, jsdir) {
        return [this.autopath(themeName, 'skin') + 'js/' + jsdir + '/**/*.js'];
    },
    jsFilesFallbackCopy: function(themeName, jsdir) {
        if(theme[themeName].jsdirs.length > 0) {
            var sourceStringArray = [],
                self = this,
                i = 0;

                _.each(theme[themeName].theme_fallback, function(theme_fallback){
                    if(theme[theme_fallback].grunt && theme[theme_fallback].jsdirs.length > 0) {
                        for(var f = 0; f < theme[theme_fallback].jsdirs.length; f++) {
                            sourceStringArray[i] = self.autopath(theme_fallback, 'skin') + 'js/' + jsdir + '*.js';
                        }
                        i++;
                    }
                });

                _.each(modules, function(modExports){
                    if(modExports.grunt && modExports.jsdirs.length > 0) {
                        for(var f = 0; f < theme[themeName].jsdirs.length; f++) {
                            sourceStringArray[i] = self.autopathModule(modExports, 'skin') + 'js/' + jsdir + '*.js';
                        }
                        i++;
                    }
                });
        }
        return sourceStringArray;
    },
    themeTasksJs: function(themeName) {
            var preTasks = ['jshint:' + themeName, 'clean:' + themeName + 'Prepare'],
                postTasks = ['uglify:' + themeName + 'Dev', 'clean:' + themeName + 'Prepare'],
                midTasks = [],
                tasks = [];

                _.each(theme[themeName].jsdirs, function(jsdir){
                    midTasks.push('copy:' + themeName + 'BuildFallback'+ jsdir);
                    midTasks.push('copy:' + themeName + 'BuildTheme'+ jsdir);
                });

                tasks = preTasks.concat(midTasks, postTasks);

                return tasks;
    }
};
