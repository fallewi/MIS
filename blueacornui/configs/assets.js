/**
* @package     BlueAcorn/GreenPistachio
* @version
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

'use strict';

var themes = require('./themes'),
    path = require('./path'),
    combo = require('./combo');

module.exports = {
    getAssets: function(normalizePath) {
        var assets = [],
            self = this;

        for(var i in themes){
            if(themes[i].grunt === true){
                assets = this.concatNarrays(assets, combo.jsMinFiles(i), combo.cssFiles(i));
            }
        }

        return normalizePath ? assets.map(this.normalizePath) : assets;
    },

    getAssetDirs: function() {
        var dirs = [];

        for(var i in themes){
            if(themes[i].grunt === true){
                dirs.push(combo.autopath(i, 'skin') + "css");
                dirs.push(combo.autopath(i, 'skin') + "jsmin");
            }
        }

        return dirs.map(this.normalizePath);
    },

    normalizePath: function(pathToFile) {
        return pathToFile.replace(path.webroot, "");
    },

    concatNarrays: function(args) {
        args = Array.prototype.slice.call(arguments);
        var newArr = args.reduce(function(prev, next) {
            return prev.concat(next) ;
        });

        return newArr;
    }
}
