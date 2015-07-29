/**
 * @package     Blueacorn/StyleGuide
 * @version     2.0
 * @author      Blue Acorn <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn.
 */

function StyleGuide(options) {
    this.init(options);
}

jQuery(document).ready(function ($) {

    StyleGuide.prototype = {
        init: function (options) {
            this.settings = {
                'moduleName' : 'StyleGuide'
            };

            // Overrides the default settings
            ba.overrideSettings(this.settings, options);

            // Start the debugger
            ba.setupDebugging(this.settings);

            this.fixSidebarOnResize();
        },

        fixSidebarOnResize: function() {
            $(window).on('resize', function(){
            if($('.main > .col-left.sidebar').length > 0){
                    $('.main > .col-left.sidebar').remove();
                }
            });
        }

    };

    /**
     * The parameter object is optional.
     * Must be an object.
     */
    if($('.cms-style-guide').length > 0) {
        ba.StyleGuide = new StyleGuide({});
    }

});