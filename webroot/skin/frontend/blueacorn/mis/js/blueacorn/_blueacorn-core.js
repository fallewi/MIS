/**
* @package     Blueacorn\BlueAcornCore
* @version     1.0
* @author      Blue Acorn <code@blueacorn.com>
* @copyright   Copyright © 2015 Blue Acorn.
*/

function BlueAcornCore(options) {
    this.init(options);
}

BlueAcornCore.prototype = {
    init: function (options) {
        this.settings = {
            'debug': false,
            'moduleName' : 'BlueAcornCore',
        };

        // Overrides the default settings
        this.overrideSettings(this.settings, options);

        // Start the debugger
        if (this.settings.debug === true) {
            this.setupDebugging(this.settings);
        }
    },

    /**
     * Takes default settings in object scope, and
     * merges the optional object passed in on initiation
     * of the class.
     */
    overrideSettings: function (settings, options) {
        if (typeof options === 'object') {
            settings = jQuery.extend(settings, options);
        }
    },

    setupDebugging: function (moduleSettings) {
        if(typeof moduleSettings === 'object'){
            this.watchConsole(moduleSettings.moduleName + ' Loaded!!!');
            this.watchConsole(moduleSettings);
        }
    },

    /**
     * Adds console log if degubbing is true
     * @param string
     */
    watchConsole: function (message) {
        console.log(message);
    }

};

/**
 * The parameter object is optional.
 * Must be an object.
 */
var ba = new BlueAcornCore({
    "debug": mageConfig["styleguide/development/enable_development"] > 0 ? true : false
});