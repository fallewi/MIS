/**
 * @package     Blueacorn/layeredNavigation
 * @version     1.0
 * @author      Blue Acorn <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn.
 */

if(typeof ba === 'undefined') {
    var ba = {};
}

function layeredNavigation(options) {
    this.init(options);
}

jQuery(document).ready(function ($) {

    layeredNavigation.prototype = {
        init: function (options) {
            this.settings = {
                'moduleName': 'layeredNavigation'
            };

            // Overrides the default settings
            ba.overrideSettings(this.settings, options);

            // Start the debugger
            ba.setupDebugging(this.settings);

            if ( $('body').hasClass('catalog-category-view') || $('body').hasClass('catalogsearch-result-index') ) {
                this.toggleFilter();
            }
        },

        toggleFilter: function(){

            $('#narrow-by-list dt').on('click', function() {

                var self = $(this),
                    filters = 'current',
                    items = 'current',
                    itemBlock = 'dd';

                if(!$(this).hasClass('child')) {
                    self.toggleClass(filters);
                    self.next(itemBlock).toggleClass(items);
                }

            });

        }
    };

    /**
     * The parameter object is optional.
     * Must be an object.
     */
    ba.layeredNavigation = new layeredNavigation({});

});