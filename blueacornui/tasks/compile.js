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

    grunt.registerTask('compile', 'Theme Compilation', function() {
        if(arguments[0]) {
            grunt.task.run('concurrent:' + arguments[0] + 'Sass');
            grunt.task.run('concurrent:' + arguments[0] + 'Postcss');
            grunt.task.run('jshint:' + arguments[0]);
            grunt.task.run('uglify:' + arguments[0] + 'Dev');
            grunt.task.run('concurrent:' + arguments[0] + 'Images');
            grunt.task.run('shell:cache');
        }else{
            _.each(themes, function(theme, name){
                if(theme.grunt) {
                    grunt.task.run('concurrent:' + name + 'Sass');
                    grunt.task.run('concurrent:' + name + 'Postcss');
                    grunt.task.run('jshint:' + name);
                    grunt.task.run('uglify:' + name + 'Dev');
                    grunt.task.run('concurrent:' + name + 'Images');
                }
            });
            grunt.task.run('shell:cache');
        }
    });
};
