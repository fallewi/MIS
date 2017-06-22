/**
* @package     BlueAcorn/GreenPistachio
* @version     4.5.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

'use strict';

var combo  = require('./combo'),
    themes = require('./themes'),
    modules = require('./modules'),
    _      = require('underscore');

var themeOptions = {};

_.each(themes, function(theme, name) {
    if(theme.grunt) {
        var fileOptions = [];
        if(theme.jsdirs) {
            _.each(theme.jsdirs, function(jsdir){
                fileOptions.push(combo.autopath(name, 'skin') + 'js/' + jsdir + '/**/*.js');
            });

            _.each(modules, function(modExports){
                _.each(modExports.jsdirs, function(jsdir){
                    fileOptions.push(combo.autopathModule(modExports, 'skin') + 'js/' + jsdir + '/**/*.js');
                });
            });

            themeOptions[name] = {
                files: {
                    src: fileOptions
                }
            };
        }
    }
});

var jshintOptions = {
    options: {
        "globals": {
            "jQuery": true,
            "prototypejs": true,
            "$": true,
            "$$": true,
            "$j": true
        },
        "evil": true,
        "expr": true,
        "nonew": true,
        "newcap": false
    }
};

module.exports = _.extend(themeOptions, jshintOptions);
