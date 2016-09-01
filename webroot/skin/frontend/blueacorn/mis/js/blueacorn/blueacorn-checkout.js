function BlueAcornCheckout() { this.init(); }
function BlueAcornInputLimit() { this.init(); }

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

    BlueAcornInputLimit.prototype = {
        init: function(){

            var self = this;

            self.setupObservers();
        },

        setupObservers: function(){
            this.limitCharacters();
        },

        limitCharacters: function(){
            $('#billing\\:street2').closest('.wide').remove();
            $('#checkout-step-billing ul').find('#billing\\:firstname, #billing\\:middlename, #billing\\:lastname, #billing\\:company, #billing\\:street1').addClass('char-limit');
            $('#checkout-step-billing ul').find('input[type="text"]').each(
                  function() {
                      if($(this).hasClass('char-limit')) {
                          $(this).bind('keypress', function (event) {
                              $('.validation-advice').remove();
                              var regex = new RegExp("^[a-zA-Z0-9 -]+$");
                              var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                              if (!regex.test(key)) {
                                  event.preventDefault();
                                  $(this).after('<div class="validation-advice" id="advice-validate-email-map_email">Please only use Letters and Numbers</div>');
                                  return false;
                              }
                          });
                      }
                  }
            );

            $('#shipping\\:street2').closest('.wide').remove();
            $('#checkout-step-shipping ul').find('#shipping\\:firstname, #shipping\\:middlename, #shipping\\:lastname, #shipping\\:company, #shipping\\:street1').addClass('char-limit');
            $('#checkout-step-shipping ul').find('input[type="text"]').each(
                function() {
                    if($(this).hasClass('char-limit')) {
                        $(this).bind('keypress', function (event) {
                            $('.validation-advice').remove();
                            var regex = new RegExp("^[a-zA-Z0-9 -]+$");
                            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                            if (!regex.test(key)) {
                                event.preventDefault();
                                $(this).after('<div class="validation-advice" id="advice-validate-email-map_email">Please only use Letters and Numbers</div>');
                                return false;
                            }
                        });
                    }
                }
            );
        }


    };

    ba.Checkout = new BlueAcornCheckout();
    ba.CharacterCheck = new BlueAcornInputLimit();

});