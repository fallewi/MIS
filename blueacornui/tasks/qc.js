/**
* @package     BlueAcorn/GreenPistachio
* @version     4.5.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

module.exports = function(grunt) {
    'use strict';

    var _ = require('underscore'),
        path = require('path'),
        themes = require('../configs/themes'),
        configDir = '../configs',
        helper = require('./helper');

    grunt.registerTask('qc', 'Evaluate Theme Javascript', function() {
        if(arguments[0]) {
            helper.runTasks(arguments[0], 'qc', grunt);
        }else{
            _.each(themes, function(theme, name){
                if(theme.grunt) {
                    helper.runTasks(name, 'qc', grunt);
                }
            });
        }
    });
};
