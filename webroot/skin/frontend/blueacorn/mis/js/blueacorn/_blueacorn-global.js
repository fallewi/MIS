function BlueAcornGlobal() { this.init(); }

jQuery(document).ready(function($){

    BlueAcornGlobal.prototype = {
        init: function(){

            var self = this;

            self.bp = 650;

            self.setupObservers();
            self.setupFooter();
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
        },

        setupFooter: function(){
            
            var self = this;
            
            self.flagSet = false;

            self.checkForWidth(self.flagSet);

            $(window).resize(function(){
                self.checkForWidth(self.flagSet);                
            });
        },

        checkForWidth: function(flagSet){
            var self = this;

            if($(window).innerWidth() <= self.bp && flagSet === false){
                self.setupAccordion();
                self.flagSet = true;
            }
        },

        setupAccordion: function(){
            $('.footer .links h5').on('click', function(){
                $(this).toggleClass('open-links');
            });
        }


    };

    ba.Global = new BlueAcornGlobal();

});