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

            this.setDescriptionContainerMaxHeight();
            this.expandDescription();
        },

        setDescriptionContainerMaxHeight: function() {
            var self = this,
                wrapper = $('.description'),
                container = $('.description .detail'),
                divHeight = $('.specifications');

            container.css({ 'max-height': (divHeight.height() - 93) });

            if (container[0].scrollHeight > container.innerHeight()) {
                wrapper.append('<div class="read-more">Read More</div>');
                $('.read-more').css({ top: (divHeight.height() - 60) });
            }
        },

        expandDescription: function() {
            var self = this,
                readMore = $('.read-more'),
                container = $('.description .detail'),
                newHeight = $('.detail')[0].scrollHeight;

            readMore.on('click', function(){
                container.css({ 'max-height': newHeight });
                readMore.detach();
            });
        }
    };

    /**
     * The parameter object is optional.
     * Must be an object.
     */
    ba.productPage = new productPage({});

});