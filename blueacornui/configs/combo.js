/**
* @package     BlueAcorn/GreenPistachio
* @version     4.3.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

'use strict';

var _ = require('underscore'),
    theme = require('./themes'),
    path = require('./path');

module.exports = {
    autopath: function(themeName, folder) {
        return path.webroot + folder + '/' +
            theme[themeName].area + '/' +
            theme[themeName].name + '/';
    },
    cssFiles: function(themeName) {
        var cssStringArray = [],
            i = 0,
            v = theme[themeName].files.length;

        for (i; i < theme[themeName].files.length; i++) {
            cssStringArray[i] = this.autopath(themeName, 'skin') +
                'css/' + theme[themeName].files[i] + '.css';
        }

        for (i = 0; i < theme[themeName].ieFiles.length; i ++) {
            cssStringArray[v] = this.autopath(themeName, 'skin') + 'css/' + theme[themeName].ieFiles[i] + '.css';
            v++;
        }

        return cssStringArray;
    },
    scssFiles: function(themeName) {
        var scssStringArray = [],
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

                    sourceStringArray[i] = this.autopath(themeName, 'skin') + 'js/' + theme[themeName].jsdirs[i] + '/**/*.js';

                    jsFiles[minStringArray[i]] = sourceStringArray[i];
                }
                return jsFiles;
        }
    },
    ieFiles: function(themeName) {
        var scssStringArray = [],
            cssStringArray = [],
            scssFiles = {},
            i = 0;

        for(i; i < theme[themeName].ieFiles.length; i++) {
            cssStringArray[i] = this.autopath(themeName, 'skin') +
                'css/' + theme[themeName].ieFiles[i] + '.css';

            scssStringArray[i] = this.autopath(themeName, 'skin') +
                'scss/' + theme[themeName].ieFiles[i] + '.scss';

            scssFiles[cssStringArray[i]] = scssStringArray[i];
        }
        return scssFiles;
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
    }
};
