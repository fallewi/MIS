/**
* @package     BlueAcorn/GreenPistachio
* @version     4.5.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

'use strict';

var combo  = require('./combo'),
    themes = require('./themes'),
    _      = require('underscore');

var themeOptions = {};

_.each(themes, function(theme, name) {
    if(theme.grunt) {
        _.each(theme.jsdirs, function(jsdir, idx){
            themeOptions [name + 'BuildFallback' + jsdir] = {
                expand: true,
                flatten: true,
                src: combo.jsFilesFallbackCopy(name, jsdir),
                dest: combo.autopath(name, 'skin') + 'js/build/' + jsdir
            };

            themeOptions[name + 'BuildTheme' + jsdir] = {
                expand: true,
                flatten: true,
                src: combo.jsFilesCopy(name, jsdir),
                dest: combo.autopath(name, 'skin') + 'js/build/' + jsdir
            };
        });
    }
});

var copyOptions = {};

module.exports = _.extend(themeOptions, copyOptions);
