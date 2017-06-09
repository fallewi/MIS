/**
* @package     BlueAcorn/GreenPistachio
* @version     4.5.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2016 Blue Acorn, Inc.
*/

'use strict';

var combo  = require('./combo'),
    themes = require('./themes'),
    _      = require('underscore');

var themeOptions = {};

_.each(themes, function(theme, name) {
    if(theme.grunt) {
        themeOptions[name + 'Prepare'] = combo.autopath(name, 'skin') + 'js/build';
    }
});

var sassOptions = {
    options: {
        force: true
    }
};

module.exports = _.extend(themeOptions, sassOptions);
