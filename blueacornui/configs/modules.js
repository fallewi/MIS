/**
* @package     BlueAcorn/GreenPistachio
* @version     4.5.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

var modules = {},
    path = require('path'),
    modulesDir = path.resolve(__dirname, "../modules");

require('fs').readdirSync(modulesDir).forEach(function(file){
    var moduleName = file.replace('.js','');
    modules[moduleName] = require(modulesDir + '/' + file);
});

module.exports = modules;
