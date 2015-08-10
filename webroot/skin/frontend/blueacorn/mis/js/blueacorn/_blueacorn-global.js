function BlueAcornGlobal() { this.init(); }

jQuery(document).ready(function($){

    BlueAcornGlobal.prototype = {
        init: function(){

            var self = this;

            self.setupObservers();
        },

        setupObservers: function(){

            this.moveMobileNavigation();
        },

        moveMobileNavigation: function(){
            var self = this;
            enquire.register('screen and (max-width:' + (bp.medium) + 'px)', {
                match: function() {
                    $('.logo').after($('#header-nav'));
                    $('#header-nav').prepend($('.store-phone'));
                },
                unmatch: function() {
                    $('.skip-links').after($('#header-nav'));
                    $('.account-cart-wrapper').prepend($('.store-phone'));
                }
            });
        }


    };

    ba.Global = new BlueAcornGlobal();

});