function BlueAcornCheckout() { this.init(); }

jQuery(document).ready(function($){

    BlueAcornCheckout.prototype = {
        init: function(){

            var self = this;

            self.setupObservers();
        },

        setupObservers: function(){
            this.triggerPrintedCardGiftOptionPrice();            
        },

        // TRIGGER ADD PRINTED PRICE WHEN CLICKING THE LABEL BECAUSE WE HIDE THE ACTUAL CHECK BOX W/ BETTER CUSTOM FORMS MODULE
        triggerPrintedCardGiftOptionPrice: function(){
            $(document).on('section:shipping_method', function() {
                setTimeout(function(){
                    $('label[for^="add-printed-card"]').on('click', function(){
                        $('input[id^="add-printed-card"]').click();
                    });
                }, 2000);
            });
        }


    };

    ba.Checkout = new BlueAcornCheckout();

});