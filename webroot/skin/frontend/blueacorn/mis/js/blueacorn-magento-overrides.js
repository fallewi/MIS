Event.observe(window, 'load', function () {

    // Prototype for Magento
    // -- Configurable Product JS Updates
    if(typeof Product !== "undefined") {
        if(typeof Product.Config !== "undefined") {
            Product.Config.prototype.resetChildren = Product.Config.prototype.resetChildren.wrap(
                function(parentFunction, element) {
                    parentFunction(element);
                    jQuery(document).trigger('update:selects');
                }
            );
            Product.Config.prototype.configureForValues = Product.Config.prototype.configureForValues.wrap(
                function(parentFunction) {
                    parentFunction();
                    this.settings.each(function(element){
                        jQuery(element).on('change', this.configure.bind(this));
                    }.bind(this));
                }
            );
            Product.Config.prototype.configureElement = Product.Config.prototype.configureElement.wrap(
                function(parentFunction, element){
                    parentFunction(element);
                    jQuery(document).trigger('update:selects');
                }
            );
        }
    }

    // -- Bundled Products
    if(typeof Product !== "undefined"){
        if(typeof Product.Bundle !== "undefined") {
            Product.Bundle.prototype.changeSelection = Product.Bundle.prototype.changeSelection.wrap(
                function(parentFunction, selection){
                    parentFunction(selection);
                    jQuery(document).trigger('update:inputs');
                }
            );
        }
    }

    if($$('.catalog-product-view').length > 0 && typeof Enterprise !== "undefined"){
        if(typeof Enterprise.Bundle !== "undefined") {
            Enterprise.Bundle.selection = Enterprise.Bundle.selection.wrap(
                function(parentFunction, optionId, selectionId){
                    parentFunction(optionId, selectionId);
                    jQuery(document).trigger('update:inputs');
                }
            );
        }
    }

    // -- Form Validation
    if(typeof Validation !== "undefined"){
        Validation.prototype.initialize = Validation.prototype.initialize.wrap(
            function(parentFunction, form, options){
                parentFunction(form, options);
                if(this.options.immediate) {
                    Form.getElements(this.form).each(function(input) {
                        if(input.tagName.toLowerCase() == 'select') {
                            jQuery(input).on('custom:blur', Validation.validate(this));
                        }
                    }, this);
                }
            }
        );
    }

    // -- Checkout
    if(typeof Checkout !== "undefined") {
        Checkout.prototype.gotoSection = Checkout.prototype.gotoSection.wrap(
            function(parentFunction, section, reloadProgressBlock) {
                parentFunction(section, reloadProgressBlock);
                jQuery(document).trigger('section:update');
                jQuery(document).trigger('section:' + section);

                if(this.currentStep === "shipping_method"){
                    window.setTimeout(function(){
                        jQuery(document).trigger('section:update');
                    }, 100);
                }
            }
        );
    }

    // -- Checkout Payment Customer Balance Checkbox
    if(typeof payment !== "undefined") {
        jQuery('label[for="use_customer_balance"]').on('click', function(){
            payment.switchCustomerBalanceCheckbox();
        });
    }

    // -- Checkout Shipping
    if(typeof Shipping !== "undefined") {
        Shipping.prototype.syncWithBilling = Shipping.prototype.syncWithBilling.wrap(
            function(parentFunction){
                parentFunction();
                jQuery(document).trigger('update:selects');
            }
        );
        Shipping.prototype.nextStep = Shipping.prototype.nextStep.wrap(
            function(parentFunction, transport) {
                parentFunction(transport);
                jQuery(document).trigger('section:update');
            }
        );
    }

    if(typeof shipping !== "undefined") {
        shipping.setSameAsBilling = shipping.setSameAsBilling.wrap(
            function(parentFunction, flag) {
                parentFunction(flag);
                jQuery(document).trigger('update:selects');
            }
        );
        shipping.syncWithBilling = shipping.syncWithBilling.wrap(
            function(parentFunction) {
                parentFunction();
                jQuery(document).trigger('update:selects');
            }
        );
    }

    if(typeof Billing !== "undefined") {
        Billing.prototype.nextStep = Billing.prototype.nextStep.wrap(
            function(parentFunction, transport) {
                parentFunction(transport);
                jQuery(document).trigger('section:update');
            }
        );
    }

    if(typeof ShippingMethod !== "undefined") {
        ShippingMethod.prototype.nextStep = ShippingMethod.prototype.nextStep.wrap(
          function(parentFunction, transport) {
              parentFunction(transport);
              jQuery(document).trigger('section:update');
          }
        );
    }

    // -- Related Products Checkboxes
    if($$('.related-checkbox').length > 0){
        jQuery.each(jQuery('.related-checkbox'), function(idx, el){
            if(jQuery(el).siblings('label, span.label').length === 0) {
                jQuery(el).after('<label for="' + jQuery(el).attr('name') + '"></label>');
                jQuery(document).trigger('update:checkboxes');
            }
        });
    }

    // -- Fix issue with Elevate Zoom on Review Pages
    if(typeof ProductMediaManager !== "undefined"){
        ProductMediaManager.createZoom = ProductMediaManager.createZoom.wrap(
            function(parentFunction, image) {
                if($$('.catalog-product-view').length) {
                    parentFunction(image);
                }
            }
        );
    }


}.bind(window));


(function(){
    // -- RegionUpdater
    if(typeof RegionUpdater !== "undefined") {
        RegionUpdater.prototype.update = RegionUpdater.prototype.update.wrap(
            function(parentFunction){
                parentFunction();
                jQuery(document).trigger('update:selects');
            }
        );
    }
})(jQuery);
