/**
 * @package     Blueacorn/categoryLandingPage
 * @version     1.0
 * @author      Blue Acorn <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn.
 */

if(typeof ba === 'undefined') {
    var ba = {};
}

function categoryLandingPage(options) {
    this.init(options);
}

jQuery(document).ready(function ($) {

    categoryLandingPage.prototype = {
        init: function (options) {
            this.settings = {
                'moduleName': 'categoryLandingPage'
            };

            // Overrides the default settings
            ba.overrideSettings(this.settings, options);

            // Start the debugger
            ba.setupDebugging(this.settings);

            if ( $('body').hasClass('catalog-category-view') ) {
                this.toggleFilter();
                this.movePromoBlock();
                this.moveDescription();
            }
        },

        toggleFilter: function(){

            $('ol.parent-child').on('click', function() {

                var self = $(this),
                    filters = 'current',
                    items = 'current',
                    itemBlock = 'dd';

                self.toggleClass(filters);
                self.children(itemBlock).toggleClass(items);
            });
        },

        movePromoBlock: function() {
            var self = this,
                title = $('.category-title'),
                block = $('.category-promo-block');

            block.detach();
            block.appendTo(title);
        },

        moveDescription: function() {
            var self = this,
                block = $('.category-description'),
                grid = $('.subcategories');

            block.detach();
            block.insertAfter(grid);
        }
    };

    /**
     * The parameter object is optional.
     * Must be an object.
     */
    ba.categoryLandingPage = new categoryLandingPage({});

});