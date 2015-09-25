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

        mapPricingObserver: function(baseUrl){
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
                    $('.validation-advice').remove();
                    window.location.replace(baseUrl + '&email=' + emailString);
                }
            });
        },

        mapPricingValidate: function(emailField){
            var regexMatch = /^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(emailField);

            return regexMatch;
        }
    };

    /**
     * The parameter object is optional.
     * Must be an object.
     */
    ba.productPage = new productPage({});

});