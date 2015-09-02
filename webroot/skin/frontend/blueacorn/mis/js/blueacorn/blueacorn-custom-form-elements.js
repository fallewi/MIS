/**
 * @package     Blueacorn/CustomFormElements
 * @version     2.0
 * @author      Blue Acorn <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn.
 */

function CustomFormElements(options) {
    this.init(options);
}

jQuery(document).ready(function ($) {

    CustomFormElements.prototype = {
        init: function (options) {
            this.settings = {
                'moduleName': 'CustomFormElements',
                'superSelects': false,
                'blackList': ['.no-style']
            };

            // Overrides the default settings
            ba.overrideSettings(this.settings, options);

            // Start the debugger
            ba.setupDebugging(this.settings);

            this.setObservers();
            this.updateAll();
        },

        setObservers: function() {
            var self = this;

            $(document).on('update:radios', function(){
                self.customRadios();
                self.unsetCustom();
            });

            $(document).on('update:checkboxes', function(){
                self.customCheckboxes();
                self.unsetCustom();
            });

            $(document).on('section:update', this.updateAll.bind(this));
            $(document).on('update:inputs', this.updateAll.bind(this));

            if(!this.settings.superSelects){
                $(document).on('update:selects', function(){
                    self.updateSelects();
                    self.updateShivs();
                    self.unsetCustom();
                });

                $(window).on('resize', function(){
                    self.updateSelects();
                    self.unsetCustom();
                });
            }
        },

        updateAll: function() {
            // Update All Custom Form Elements, your update your settings
            // objects in the CustomFormElements & SuperSelects Modules
            this.customRadios();
            this.customCheckboxes();
            if(!this.settings.superSelects) {
                this.customSelects();
                this.updateShivs();
            }
            this.unsetCustom();
        },

        getLabel: function(el) {
            var elementLabel;

            if($(el).siblings('label, span.label').length){
                elementLabel = $(el).siblings('label, span.label').first();
            } else if($(el).parent().siblings('label, span.label').length) {
                elementLabel = $(el).parent().siblings('label, span.label').first();
            } else if($(el).parent('label').length){
                elementLabel = $(el).parent('label');
            } else {
                $(el).after('<label for="' + $(el).attr('id') + '"></label>');
                elementLabel = $(el).siblings('label');
            }

            return elementLabel;
        },

        getLabelType: function(el) {
            var labelType = 'label';

            if(($(el).siblings('span.label').length) || ($(el).parent().siblings('span.label').length)) {
                labelType = 'span';
            }

            return labelType;
        },

        resetLabels: function(el, type) {
            $(el).removeClass(type + ' disabled checked');
        },

        setProperties: function(labelElement, labelInput, prop) {
            if($(labelInput).prop(prop) && labelElement) {
                $(labelElement).addClass(prop);
            }
        },

        getLabelInput: function(labelType, labelElement) {
            var labelFor, labelInput;

            if(labelType === 'label') {
                labelFor = $(labelElement).attr('for');
                if(labelFor.indexOf(':') != '-1') {
                    labelFor = labelFor.replace(':', '\\:');
                }
                labelInput = $('#' + labelFor + ', input[name="' + labelFor + '"]');
            } else {
                labelInput = $(labelElement).siblings('input');
            }

            return labelInput;
        },

        customRadios: function() {
            var radioElements =  $('input[type="radio"].radio'), self = this;

            if($(radioElements).length > 0){
                $(radioElements).each(function(idx, el){
                    var radioLabel, labelType;

                    radioLabel = self.getLabel(el);
                    labelType = self.getLabelType(el);

                    this.resetLabels(radioLabel, 'radio-label');
                    this.setProperties(radioLabel, el, 'checked');
                    this.setProperties(radioLabel, el, 'disabled');

                    if(radioLabel){
                        $(radioLabel).addClass('radio-label');
                        $(el).addClass('input-custom');

                        $(radioLabel).on('click', function(event){
                            event.stopPropagation();
                            event.preventDefault();

                            var labelRadio = $(event.target).closest(labelType),
                                radioInput = this.getLabelInput(labelType, labelRadio),
                                groupRadio = $(radioInput).attr('name');

                            if(!$(radioInput).prop('disabled')){
                                this.updateRadioGroup(groupRadio, labelType);

                                $(radioInput).prop('checked',true);
                                $(labelRadio).addClass('checked');
                            }

                            if($(radioInput).attr('onclick')){
                                $(radioInput).trigger('click');
                            }

                        }.bind(this));
                    }
                }.bind(this));
            }
        },

        updateRadioGroup: function(group, labelType){
            // Method to uncheck all radios in a group and
            // remove any associated checked state classes
            // from their labels.
            $('input[name="' + group + '"]').each(function(idx, el){
                $(el).prop('checked', false);
                $(el).siblings(labelType).removeClass('checked');
                if($(el).parent('label').length > 0){
                    $(el).parent('label').removeClass('checked');
                }
            });
        },

        customCheckboxes: function(){
            var self = this, checkboxElements = $('input[type="checkbox"].checkbox');

            if($(checkboxElements).length > 0){
                $(checkboxElements).each(function(idx, el){
                    var checkboxLabel, labelType;

                    checkboxLabel = self.getLabel(el);
                    labelType = self.getLabelType(el);

                    this.resetLabels(checkboxLabel, 'checkbox-label');
                    this.setProperties(checkboxLabel, el, 'checked');
                    this.setProperties(checkboxLabel, el, 'disabled');

                    if(checkboxLabel){
                        $(checkboxLabel).addClass('checkbox-label');
                        $(el).addClass('input-custom');

                        $(checkboxLabel).off('click').on('click', function(event){
                            event.stopPropagation();
                            event.preventDefault();

                            var labelCheckbox = $(event.target).closest(labelType),
                                checkboxInput = this.getLabelInput(labelType, labelCheckbox);

                            if($(checkboxInput).prop('checked')){
                                $(labelCheckbox).removeClass('checked');
                                $(checkboxInput).prop('checked', false);
                            }else{
                                $(labelCheckbox).addClass('checked');
                                $(checkboxInput).prop('checked', true);
                            }

                            if($(checkboxInput).attr('onclick')){
                                 $(checkboxInput)[0].onclick.apply($(checkboxInput)[0]);
                            }
                        }.bind(this));
                    }
                }.bind(this));
            }
        },

        customSelectsBefore: function(){
            var smallSelects = $('#select-language, .toolbar select, .review-heading .pager select, .review-customer-index .pager select, .small-select');

            // Add Class for Selects that would normally need to be smaller in designs.
            $(smallSelects).addClass('sm');
        },

        customSelects: function(){
            this.customSelectsBefore();

            var selectElements = $('select'), selectTruncate = 36;

            if($(selectElements).length > 0){
                $(selectElements).each(function(idx, el){

                    var selectTitle, selectedOptions;

                    if($(el).parent('.select-container').length > 0 || $(el).prop('multiple')){
                        return;
                    }

                    if($(el).attr('title')){
                        selectTitle = $(el).attr('title').strip().truncate(selectTruncate);
                    }

                    $(el).addClass('select-custom').data('truncate', selectTruncate);

                    if($(el).prev().length > 0 && (!$(el).parent().hasClass('input-box') || !$(el).parent().hasClass('v-fix'))){
                        $(el).wrap('<div class="input-box"></div>');
                    }

                    $(el).parent().addClass('select-container');

                    if($(el).prop('disabled')){
                        $(el).parent().addClass('disabled');
                    }else{
                        $(el).parent().removeClass('disabled');
                    }

                    if($(el).hasClass('sm')){
                        $(el).parent().addClass('small');
                    }

                    selectTitle = $(el).children().first().html().strip().truncate(selectTruncate);

                    selectedOptions = $(el).children('option:selected').text();

                    if(selectedOptions.length > 0){
                        selectTitle = selectedOptions.strip().truncate(selectTruncate);
                    }

                    if($(el).siblings('.custom-shiv').length === 0){
                        var selectSize = '';
                        $(el).before('<span class="custom-shiv">' + selectTitle + '<span></span></span>');
                    }

                    $(el).on('change', this.updateShivs.bind(this));
                    $(el).on('mouseover', function(){
                        $(el).siblings('.custom-shiv').addClass('hover');
                    });
                    $(el).on('mouseout', function(){
                        $(el).siblings('.custom-shiv').removeClass('hover');
                    });

                    this.updateSelects();

                }.bind(this));
            }
        },

        updateShivs: function(){
            var selectShivs = $('.custom-shiv');

            $(selectShivs).each(function(){
                var selectElement, optionValue, truncateOption;

                selectElement = $(this).siblings('select');
                optionValue = $(selectElement).children('option:selected').text();
                truncateOption = $(selectElement).data('truncate');

                $(this).html(optionValue.strip().truncate(truncateOption) + '<span></span>');
            });
        },

        updateSelects: function(){
            var selectShivs = $('.custom-shiv');

            $(selectShivs).each(function(){
                if($(this).siblings('select').css('display') === 'none'){
                    $(this).css('display','none');
                }else{
                    $(this).css('display','');
                }

                $(this).parent('.select-container').removeClass('disabled');

                if($(this).siblings('select').prop('disabled')){
                    $(this).parent('.select-container').addClass('disabled');
                }
            });
        },

        unsetCustom: function() {
            var self = this;

            $.each(self.settings.blackList, function(idx, listItem){

                $.each($(listItem), function(idx, el){
                    if($(el).prop('tagName') == "SELECT"){
                        self.unsetCustomSelect(el);
                    } else {
                        self.unsetCustomInput(el);
                    }
                });
            });
        },

        unsetCustomSelect: function(el) {

            // Remove Additional Styling from Parent Container
            $(el).parent('.select-container').removeClass('select-container');

            // Remove Custom Styling from Select Element
            $(el).removeClass('custom-select select-custom disabled');

            $(el).siblings('.custom-shiv').remove();

        },

        unsetCustomInput: function(el) {
            $(el).removeClass('input-custom');

            if($(el).siblings('.radio-label, .checkbox-label').length > 0){
                $(el).siblings('.radio-label, .checkbox-label').removeClass('radio-label checkbox-label');
            }else if($(el).parent().siblings('.radio-label, .checkbox-label').length > 0){
                $(el).parent().siblings('.radio-label, .checkbox-label').removeClass('radio-label checkbox-label');
            }
        }
    };

    /**
     * The parameter object is optional.
     * Must be an object.
     */
    ba.CustomFormElements = new CustomFormElements({});

});