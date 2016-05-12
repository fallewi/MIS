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
        themes = require('./configs/themes'),
        configDir = './configs',
        taskDir = './tasks';

    [
        'time-grunt',
        taskDir + '/compile',
        taskDir + '/staging',
        taskDir + '/production',
        taskDir + '/qc',
        taskDir + '/githooks'
    ].forEach(function (task) {
        require(task)(grunt);
    });

    require('load-grunt-config')(grunt, {
        configPath: path.join(__dirname, configDir),
        init: true,
        jitGrunt: {
            staticMappings: {
                usebanner: 'grunt-banner'
            }
        }
    });

    _.each({

        default: function() {
            grunt.task.run('watch');
        },

        setup: function() {
            grunt.task.run('shell:setup');
            grunt.task.run('compile');
        },

        sync: function() {
            grunt.task.run('browserSync');
            grunt.task.run('watch');
        }

    }, function(task, name) {
        grunt.registerTask(name, task);
    });

};
