/**
* @package     BlueAcorn/GreenPistachio
* @version     4.3.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

'use strict';

var grunt = require('grunt'),
    pkg = grunt.file.readJSON('./package.json'),
    currentYear = grunt.template.today('yyyy'),
    nlWin = '\r\n',
    combo = require('./combo'),
    themes = require('./themes'),
    _ = require('underscore');

function baBanner() {
    return '/**' + nlWin +
    '* @package     ' + pkg.namespace + '/' + pkg.module + nlWin +
    '* @version     ' + pkg.version + nlWin +
    '* @author      ' + pkg.author.name + ' <' + pkg.author.email + '>' + nlWin +
    '* @copyright   Copyright © ' + currentYear + ' Blue Acorn, Inc.' + nlWin +
    '* @desc        This file was precompiled using modular pre-processor' + nlWin +
    '*              css and javascript' + nlWin +
    '*/';
}

var themeOptions = {};

_.each(themes, function(theme, name) {
    if(theme.grunt) {
        themeOptions[name + 'Css'] = {
            options: {
                banner: baBanner()
            },
            files: {
                src: combo.cssFiles(name)
            }
        };

        themeOptions[name + 'Js'] = {
            options: {
                banner: baBanner()
            },
            files: {
                src: combo.jsMinFiles(name)
            }
        };
    }
});

var bannerOptions = {
    options: {
        position: 'top',
        linebreak: true
    }
};

module.exports = _.extend(themeOptions, bannerOptions);
