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

    $.fn.isAfter = function(sel){
        return this.prevAll().filter(sel).length !== 0;
    };

    $.fn.isBefore= function(sel){
        return this.nextAll().filter(sel).length !== 0;
    };

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
            this.setupAccordion();
            this.moveAddToCartButton();
            this.thumb();
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
                                stagePadding: 1,
                                items: 1
                            });
                        }
                        if(upsellContainer.length > 0){
                            upsellContainer.owlCarousel({
                                loop: true,
                                dots: false,
                                center: true,
                                stagePadding: 1,
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

            container.css({ 'max-height': (divHeight.height()) });

            if (container[0].scrollHeight > container.innerHeight()) {
                wrapper.append('<div class="read-more">Read More</div>');
                $('.read-more').css({ top: (wrapper.height() + 4) });
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
        },

        setupAccordion: function(){
            var self = this,
                title = $('.column-right .column-container h2');

            title.on('click', function(){
                $(this).toggleClass('open-links');
            });
        },

        moveAddToCartButton: function() {
            var self = this,
                button = $('.add-to-cart-buttons'),
                links = $('.add-to-links');

            enquire.register('screen and (max-width:' + (bp.medium) + 'px)', {
                match: function() {
                    if($('.add-to-box .add-to-cart-buttons').isAfter('.add-to-box .add-to-links') === false) {
                        var button = $('.add-to-box .add-to-cart-buttons').detach();
                        $('.add-to-box .add-to-links').after(button);
                    }
                },
                unmatch: function() {
                    if($('.add-to-box .add-to-cart-buttons').isAfter('.add-to-links')) {
                        var button = $('.add-to-box .add-to-cart-buttons').detach();
                        $('.add-to-box .add-to-cart').append(button);
                    }
                }
            });
        },

        mapPricingObserver: function(){
            var self = this;

            $('#map_email').on('change', function(){
                var emailString = $(this).val();
                $('.validation-advice').remove();
                if(!self.mapPricingValidate(emailString)) {
                    $(this).after('<div class="validation-advice" id="advice-validate-email-map_email">Please enter a valid email address. For example johndoe@domain.com.</div>');
                }
            });

            $('#map_request').on('click', function(ev){
                ev.preventDefault();
                var emailString = $('#map_email').val();
                if(self.mapPricingValidate(emailString)) {
                    $('#advice-validate-email-map_email').remove();
                }
            });
        },

        mapPricingValidate: function(emailField){
            var regexMatch = /^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(emailField);

            return regexMatch;
        },

        thumb: function() {
            var self = this,
                thumb = $('.thumb-link img');

            thumb.on('click', function(){
                thumb.removeClass('active');
                $(this).addClass('active');
            });
        }
    };

    /**
     * The parameter object is optional.
     * Must be an object.
     */
    if(ba.Page.getPage('product')){
        ba.productPage = new productPage({});
    }

});