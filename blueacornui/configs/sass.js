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
        themeOptions[name + 'Dev'] = {
            options: {
                sourceMap: true,
                sourceMapEmbed: false,
                includePaths: combo.themeFallback(name)
            },
            files: combo.scssFiles(name)
        };

        themeOptions[name + 'Production'] = {
            options: {
                sourceMap: false,
                outputStyle: 'compact',
                includePaths: combo.themeFallback(name)
            },
            files: combo.scssFiles(name)
        };
    }
});

var sassOptions = {
    options: {
        sourceComments: false,
        precision: 4,
        outputStyle: 'expanded',
        sourceMapContents: true
    }
};

module.exports = _.extend(themeOptions, sassOptions);
