/**
 * @package     Blueacorn/StyleGuideMenu
 * @version     1.0
 * @author      Blue Acorn <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn.
 */

function StyleGuideMenu(options) {
    this.init(options);
}

jQuery(document).ready(function ($) {

    StyleGuideMenu.prototype = {
        init: function (options) {
            this.settings = {
                'moduleName': 'StyleGuideMenu'
            };

            // Overrides the default settings
            ba.overrideSettings(this.settings, options);

            // Start the debugger
            ba.setupDebugging(this.settings);

            this.buildMenu();
            this.setSectionDisplays();
            this.setMenuObservers();
        },

        buildMenu: function() {
            var self = this;
            self.buildSectionObject();

            $.each(self.sections, function(idx, currentSection){

                var sectionClass = self.getSectionStorage(idx) ? 'active-section' : '';
                var sectionActive = self.getSectionStorage(idx) ? 'checked' : '';

                var sectionSelector = '<li><input type="checkbox" class="checkbox ' + sectionClass + '"name="section' + idx + '" value="' + idx + '" ' + sectionActive + ' /><label for="section' + idx + '">' + currentSection.name + '</label></li>';
                $('.sg-menu-list').append(sectionSelector);
            });

            $(document).trigger('update:checkboxes');

            self.setObservers();
        },
        buildSectionObject: function(){
            var self = this;
            self.sections = [];

            $.each($('.col-main section'), function(idx, currentSection){

                var section = {
                    name: $(currentSection).find('h3.sg-h').text(),
                    sectionElement: $(currentSection),
                    active: self.getSectionStorage(idx)
                };

                self.sections.push(section);
            });

        },
        setObservers: function() {
            var self = this;

            $('.sg-menu-list label').on('click', function(evt){
                $(this).siblings('input').toggleClass('active-section');
                self.setSectionStorage();
                self.setSectionDisplays();
            });

            $('.sg-config-list label').on('click', function(evt){
                $('body').toggleClass('code-active');
            });
        },
        getSectionStorage: function(idx) {
            if(Modernizr.localstorage){
                if(localStorage.getItem('sgmenu')){
                    return JSON.parse(localStorage.getItem('sgmenu'))[idx] ? true : false;
                }else{
                    return true;
                }
            }else{
                return true;
            }
        },
        setSectionStorage: function() {
            var self = this;

            if(Modernizr.localstorage){
                var sections = [];
                $.each(self.sections, function(id, el){
                    sections.push($($('.sg-menu-list input')[id]).hasClass('active-section'));
                });

                localStorage.setItem('sgmenu', JSON.stringify(sections));
            }
        },
        setSectionDisplays: function() {
            var self = this;

            $.each(self.sections, function(idx, currentSection){
                var sectionClass = 'active-section';

                if(self.getSectionStorage(idx)){
                    $(currentSection.sectionElement).addClass(sectionClass);
                }else{
                    $(currentSection.sectionElement).removeClass(sectionClass);
                }
            });
        },
        setMenuObservers: function() {
            var self = this;
            $('.sg-button').on('click', function(evt){
                $('.sg-shiv').toggleClass('shiv-active');
                $('.sg-menu').toggleClass('menu-active');
                $('.sg-menu').css('margin-top', ($('.sg-menu').height()/2 - $('.sg-menu').height()));
            });
        }
    };

    /**
     * The parameter object is optional.
     * Must be an object.
     */
    if($('.cms-style-guide').length > 0) {
        ba.StyleGuideMenu = new StyleGuideMenu({});
    }

});