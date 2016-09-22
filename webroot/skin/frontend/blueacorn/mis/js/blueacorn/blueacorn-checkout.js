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
                          $(this).keydown(function (e) {
                              $('.validation-advice').remove();

                                  if ((e.keyCode >= 48 && e.keyCode <= 57 && e.shiftKey=== false) || // Allow: 0-9 exclude the symbols
                                      (e.keyCode === 188 && e.shiftKey=== false) || //Allow: comma
                                      (e.keyCode === 189 && e.shiftKey=== false) || //Allow: dash
                                      (e.charCode === "o" && e.shiftKey=== false) || //Allow: dash for FIREFOX
                                      (e.keyCode === 173 && e.shiftKey=== false) || //Allow: dash for FIREFOX
                                      (e.keyCode >= 65 && e.keyCode <= 90) || // Allow: A-Z
                                      (e.keyCode >= 32 && e.keyCode <= 40) || // Allow: space, page up, page down, end, home, left arrow, up arrow, right arrow, down arrow
                                      $.inArray(e.keyCode, [8, 9, 13, 16, 27, 46, 190 ]) !== -1 || // Allow: backspace, tab, enter, shift, escape, delete, period
                                      (e.keyCode >= 96 && e.keyCode <= 105) // Allow: number pad 0-9
                                    ) {
                                      return true;
                                  } else {
                                      e.preventDefault();
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
                        $(this).keydown(function (e) {
                            $('.validation-advice').remove();

                            if ((e.keyCode >= 48 && e.keyCode <= 57 && e.shiftKey=== false) || // Allow: 0-9 exclude the symbols
                                (e.keyCode === 188 && e.shiftKey=== false) || //Allow: comma
                                (e.keyCode === 189 && e.shiftKey=== false) || //Allow: dash
                                (e.charCode === "o" && e.shiftKey=== false) || //Allow: dash for FIREFOX
                                (e.keyCode === 173 && e.shiftKey=== false) || //Allow: dash for FIREFOX
                                (e.keyCode >= 65 && e.keyCode <= 90) || // Allow: A-Z
                                (e.keyCode >= 32 && e.keyCode <= 40) || // Allow: space, page up, page down, end, home, left arrow, up arrow, right arrow, down arrow
                                $.inArray(e.keyCode, [8, 9, 13, 16, 27, 46, 190 ]) !== -1 || // Allow: backspace, tab, enter, shift, escape, delete, period
                                (e.keyCode >= 96 && e.keyCode <= 105) // Allow: number pad 0-9
                            ) {
                                return true;
                            } else {
                                e.preventDefault();
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