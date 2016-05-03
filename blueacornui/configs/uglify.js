/**
* @package     BlueAcorn/GreenPistachio
* @version     4.3.0
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
            files: combo.jsFiles(name)
        };
        themeOptions[name + 'Production'] = {
            options: {
                mangle: false,
                compress: true,
                beautify: false,
                sourceMap: false,
                wrap: true
            },
            files: combo.jsFiles(name)
        };
    }
});

var uglifyOptions = {
    options: {
        mangle: false,
        beautify: true,
        compress: false,
        sourceMap: false,
        wrap: false
    }
};

module.exports = _.extend(themeOptions, uglifyOptions);
