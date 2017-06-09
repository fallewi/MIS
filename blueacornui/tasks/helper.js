/**
* @package     BlueAcorn/GreenPistachio
* @version     4.5.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

'use strict';

var _ = require('underscore'),
    taskList = require('./taskList'),
    themes = require('../configs/themes');

module.exports = {
    runTasks: function(themeName, type, grunt) {
        _.each(taskList[type], function(obj, idx){
            _.each(obj, function(val, task){
                if(task === "copy") {
                    if(themes[themeName].jsdirs.length > 0) {
                        _.each(themes[themeName].jsdirs, function(jsdir){
                            _.each(val, function(taskVal){
                                grunt.task.run(task + ':' + themeName + taskVal + jsdir);
                            });
                        });
                    }
                }else{
                    grunt.task.run(task + ':' + themeName + val);
                }
            })
        });
    }
};
