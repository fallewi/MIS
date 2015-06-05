/**
 * @package     Blueacorn\ResponsiveNotation
 * @version     2.0
 * @author      Blue Acorn <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn.
 */

function ResponsiveNotation(options) {
    this.init(options);
}

jQuery(document).ready(function ($) {

    ResponsiveNotation.prototype = {
        init: function (options) {
            this.settings = {
                'moduleName' : 'ResponsiveNotation',
                'mobileClass': 'resp-mobile',
                'tabletClass': 'resp-tablet',
                'desktopClass': 'resp-desktop'
            };

            // Overrides the default settings
            ba.overrideSettings(this.settings, options);

            // Start the debugger
            ba.setupDebugging(this.settings);

            // Fire document event incase you need to observer rNote being loaded before you load something else.
            $(document).trigger('rnote:loaded');

            // Setup Enquire Observers to Change Class Based on
            this.setViewportClass();
        },

        setViewportClass: function(){
            var self = this;
            enquire.register('screen and (min-width:' + (bp.large + 1) + 'px)', {
                match: function() {
                    $('html').addClass(self.settings.desktopClass);
                },
                unmatch: function() {
                    $('html').removeClass(self.settings.desktopClass);
                }
            }).register('screen and (min-width:' + (bp.small + 1) + 'px) and (max-width:' + bp.large + 'px)', {
                match: function() {
                    $('html').addClass(self.settings.tabletClass);
                },
                unmatch: function() {
                    $('html').removeClass(self.settings.tabletClass);
                }
            }).register('screen and (max-width:' + bp.small + 'px)', {
                match: function() {
                    $('html').addClass(self.settings.mobileClass);
                },
                unmatch: function() {
                    $('html').removeClass(self.settings.mobileClass);
                }
            });
        },

    };

    /**
     * The parameter object is optional.
     * Must be an object.
     */
    ba.ResponsiveNotation = new ResponsiveNotation({});

});