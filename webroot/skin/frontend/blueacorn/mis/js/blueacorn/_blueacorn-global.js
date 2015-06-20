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
                },
                unmatch: function() {
                    $('.skip-links').after($('#header-nav'));
                }
            });
        }


    };

    ba.Global = new BlueAcornGlobal();

});