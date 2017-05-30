/**
* @package     BlueAcorn/GreenPistachio
* @version     4.5.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

'use strict';

var themes = require('./themes'),
    _ = require('underscore');

var themeOptions = {},
    concurrentOptions = {};

_.each(themes, function(theme, name) {
    if(theme.grunt) {
        themeOptions[name + 'UseBanner'] = ['usebanner:' + name + 'Css', 'usebanner:' + name + 'Js'];
        themeOptions[name + 'Images'] = ['newer:imagemin:' + name + 'Dev', 'newer:svgmin:' + name + 'Dev'];
    }
});

module.exports = _.extend(themeOptions, concurrentOptions);
