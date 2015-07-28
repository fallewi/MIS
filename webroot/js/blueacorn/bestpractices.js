var bestPractice = {};

jQuery('document').ready(function($){

    var Services = function() {};
    var GeneralSettings = function(settings) { this.init(settings); };
    var RegisterForm = function(settings) { this.init(settings); };
    var ForgotPasswordForm = function(settings) {
        if(settings.enable == 1) {
            this.init(settings);
        }
    };
    var DefaultShippingMethod = function(settings) { this.init(settings); };

    Services.prototype = {
        overrideSettings: function(object, settings) {
            if(typeof settings == 'object') {
                object.settings = $.extend(object.settings, settings);
            }
        }
    };

    GeneralSettings.prototype = {
        init: function(settings) {
            this.settings = {
                'container' : $('body.checkout-onepage-index')
            };
            bestPractice.services.overrideSettings(this, settings);
            this.setDefaults();
        },
        setDefaults: function() {
            this.settings.container.addClass('blueacorn-best-practice');
        }
    };

    RegisterForm.prototype = {
        init: function(settings) {
            this.settings = {
                'registerAnchor' : $('.register-anchor'),
                'guestAnchor' : $('#onepage-guest-register-button'),
                'registerControl' : $('.register-control input[type="radio"]'),
                'guestControl' : $('.guest-control input[type="radio"]')
            };
            bestPractice.services.overrideSettings(this, settings);
            this.registerObservers();
        },
        registerObservers: function() {
            this.settings.registerAnchor.on('click', function(e) {
                this.settings.registerControl.trigger('click');
                checkout.setMethod();
            }.bind(this));
            this.settings.guestAnchor.on('click', function(e) {
                this.settings.guestControl.trigger('click');
                checkout.setMethod();
            }.bind(this));
        }
    };

    ForgotPasswordForm.prototype = {
        init: function(settings) {
            this.settings = {
                'targetAnchor' : $('.forgot-password'),
                'fancyBoxInner' : $('.fancybox-inner'),
                'forgotPasswordForm' : ''
            };
            bestPractice.services.overrideSettings(this, settings);
            this.registerObservers();
        },
        registerObservers: function() {
            this.settings.targetAnchor.on('click', function(e) {
                this.enableForm();
            }.bind(this));
        },
        enableForm: function() {
            $.fancybox.open({
                content: "<form name=\"forgot-password-form\" id=\"forgot-password-form\"><h2>" + this.settings.header + "</h2><p class=\"form-instructions\">" + this.settings.description + "</p><ul class=\"form-list\"><li><div class=\"input-box\"><input type=\"email\" autocapitalize=\"off\" autocorrect=\"off\" spellcheck=\"false\" name=\"email\" alt=\"email\" id=\"email_address\" placeholder=\"" + this.settings.placeholder + "\" class=\"input-text required-entry validate-email\" value=\"\"></div></li></ul><div class=\"buttons-set\"><button type=\"submit\" title=\"Submit\" class=\"button\"><span><span>" + this.settings.buttontext +  "</span></span></button></div><div class=\"message\"></div>",
                beforeShow : function() {
                    $(".fancybox-inner").addClass('forgot-password');
                    $(".fancybox-inner button").on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.submitForm();
                    }.bind(this));
                    this.settings.forgotPasswordForm = new VarienForm('forgot-password-form');
                }.bind(this),
            });
        },
        submitForm : function() {
            if(this.settings.forgotPasswordForm.validator.validate()) {
                $.ajax({
                    url: mageConfig.base_url + 'customer/account/forgotPasswordPost',
                    type: 'POST',
                    data: {'email' : $('#forgot-password-form #email_address').val(),
                           'ajax' : true},
                    beforeSend: function() {
                        $('.forgot-password button').prop('disabled', true);
                        $('.forgot-password button span span').html("<img src=\"" + mageConfig.media_url + "blueacorn/images/ajax-spinner.gif\" />");
                    }
                }).success(function(data) {
                    $('.forgot-password button span span').html(this.settings.buttontext);
                    $('.forgot-password button').prop('disabled', false);
                    $('.fancybox-inner .message').html('<div class="">' + data + '</div>');
                    }.bind(this)).done(function() {
                }).error(function(e) {
                    console.log(e);
                });
            }
        }
    };

    DefaultShippingMethod.prototype = {
        init: function(settings) {
            this.settings = {
                'targetEle' : $('#checkout-step-shipping_method input[type="radio"]')
            };
            bestPractice.services.overrideSettings(this, settings);
            this.setDefaultShippingMethod();
        },
        setDefaultShippingMethod: function() {
            this.settings.targetEle.first().attr('checked', 'checked');
            var labelFor = this.settings.targetEle.first().attr('id');
            this.settings.targetEle.first().siblings('label[for="'+labelFor+'"]').trigger('click');
        }
    };

    bestPractice.services = new Services();
    bestPractice.generalSettings = new GeneralSettings({});
    bestPractice.registerForm = new RegisterForm({});
    bestPractice.forgotPasswordForm = new ForgotPasswordForm(mageConfig['bestpractices/stepone/ajaxform']);

    $(window).on('section:shipping_method', function() {
        bestPractice.defaultShippingMethod = new DefaultShippingMethod({});
    });

});