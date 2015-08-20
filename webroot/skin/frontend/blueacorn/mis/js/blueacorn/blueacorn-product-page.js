/**
 * @package     Blueacorn/productPage
 * @version     1.0
 * @author      Blue Acorn <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn.
 */

if(typeof ba === 'undefined') {
    var ba = {};
}

function productPage(options) {
    this.init(options);
}

jQuery(document).ready(function ($) {

    productPage.prototype = {
        init: function (options) {
            this.settings = {
                'moduleName': 'productPage'
            };

            // Overrides the default settings
            ba.overrideSettings(this.settings, options);

            // Start the debugger
            ba.setupDebugging(this.settings);

            this.imageSlider();
        },

        imageSlider: function() {
            var self = this;

            if($(window).innerWidth() <= 431) {     //strange as it fires at 388 or less
                if($('body').hasClass('catalog-product-view')){
                    alert('test');
                }
            }
        }
    };

    /**
     * The parameter object is optional.
     * Must be an object.
     */
    ba.productPage = new productPage({});

});