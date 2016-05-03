/**
* @package     BlueAcorn/GreenPistachio
* @version     4.3.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

module.exports = function(grunt) {
    'use strict';

    var _ = require('underscore'),
        path = require('path'),
        themes = require('../configs/themes'),
        configDir = '../configs';

    grunt.registerTask('qc', 'Evaluate Theme Javascript', function() {
        if(arguments[0]) {
            grunt.task.run('jshint:' + arguments[0]);
        }else{
            _.each(themes, function(theme, name){
                if(theme.grunt) {
                    grunt.task.run('jshint:' + name);
                }
            });
        }
    });
};
