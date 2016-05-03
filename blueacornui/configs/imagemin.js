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
            expand: true,
            cwd: combo.autopath(name, 'skin') + 'src/',
            src: ['**/*.{png,jpg,gif}'],
            dest: combo.autopath(name, 'skin') + 'images/'
        };
    }
});

var imageminOptions = {
    options: {}
};

module.exports = _.extend(themeOptions, imageminOptions);
