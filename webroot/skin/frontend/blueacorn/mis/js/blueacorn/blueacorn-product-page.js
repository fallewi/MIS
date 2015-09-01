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
            this.setDescriptionContainerMaxHeight();
            this.expandDescription();
        },

        imageSlider: function() {
            var self = this,
                relatedContainer = $('.products-grid.owl-carousel-1'),
                upsellContainer = $('.products-grid.owl-carousel-2');

            if($('body').hasClass('catalog-product-view')){
                enquire.register('screen and (max-width:' + (bp.xsmall) + 'px)', {
                    match: function() {
                        if(relatedContainer.length > 0){
                            relatedContainer.owlCarousel({
                                loop: true,
                                dots: false,
                                center: true,
                                stagePadding: 10,
                                responsiveClass: true,
                                items: 1
                            });
                        }
                        if(upsellContainer.length > 0){
                            upsellContainer.owlCarousel({
                                loop: true,
                                dots: false,
                                center: true,
                                stagePadding: 10,
                                responsiveClass: true,
                                items: 1
                            });
                        }
                    },
                    unmatch: function() {
                        relatedContainer.trigger('destroy.owl.carousel').removeClass('owl-carousel-1 owl-loaded');
                        relatedContainer.find('.owl-stage-outer').children().unwrap();
                        upsellContainer.trigger('destroy.owl.carousel').removeClass('owl-carousel-2 owl-loaded');
                        upsellContainer.find('.owl-stage-outer').children().unwrap();
                    }
                });
            }
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