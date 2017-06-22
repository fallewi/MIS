/**
* @package     BlueAcorn/GreenPistachio
* @version     4.1.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

'use strict';

var path = require('./path');
var grunt = require('grunt');

var compress = {
    main: {
        options: {
            mode: 'tgz',
            archive: function () {
                return (grunt.option('dest') || '/tmp') + '/payload.tar.gz'
            }
        },
        files: [{
            expand: true,
            cwd: path.webroot,
            src: require('./assets').getAssets(true),
            dest: '/',
        }]
    }
};

module.exports = compress;
