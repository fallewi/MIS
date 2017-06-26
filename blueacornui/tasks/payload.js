/**
 * @package     BlueAcorn/GreenPistachio
 * @version     4.3.2
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2016 Blue Acorn, Inc.
 */

module.exports = function(grunt) {
    'use strict';

    var assets = require('../configs/assets');

    grunt.registerTask('payload', 'Production Theme Compilation Payload', function() {
        var dir = grunt.option('dest') || '/tmp';

        // Run the production task
        grunt.task.run('production');

        // Run the compress task
        grunt.task.run('compress');

        // deliver payload txt to doc
        grunt.file.write(dir +  '/payload.json', JSON.stringify({
            dirs: require('../configs/assets').getAssetDirs()
        }));
    });
};
