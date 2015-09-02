/**
 * @package     Blueacorn/StyleGuideColor
 * @version     2.0
 * @author      Blue Acorn <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn.
 */

function StyleGuideColor(options) {
    this.init(options);
}

jQuery(document).ready(function ($) {

    StyleGuideColor.prototype = {
        init: function (options) {
            this.settings = {
                'moduleName' : 'StyleGuideColor'
            };

            this.css = [
                {
                    'selector': '.sg-paragaphs p',
                    'attributes': ['color', 'font-size', 'line-height', 'font-family', 'font-weight']
                },
                {
                    'selector': '.sg-links a',
                    'attributes': ['color', 'font-size', 'line-height', 'font-family', 'font-style', 'font-weight']
                },
                {
                    'selector': '.sg-strong strong',
                    'attributes': ['color', 'font-size', 'line-height', 'font-family', 'font-weight']
                },
                {
                    'selector': '.sg-emphasis em',
                    'attributes': ['color', 'font-size', 'line-height', 'font-family', 'font-style']
                },
                {
                    'selector': '.headings h1',
                    'attributes': ['color', 'font-size', 'line-height', 'margin-bottom', 'font-family', 'font-weight', 'font-style']
                },
                {
                    'selector': '.headings h2',
                    'attributes': ['color', 'font-size', 'line-height', 'margin-bottom', 'font-family', 'font-weight', 'font-style']
                },
                {
                    'selector': '.headings h3',
                    'attributes': ['color', 'font-size', 'line-height', 'margin-bottom', 'font-family', 'font-weight', 'font-style']
                },
                {
                    'selector': '.headings h4',
                    'attributes': ['color', 'font-size', 'line-height', 'margin-bottom', 'font-family', 'font-weight', 'font-style']
                },
                {
                    'selector': '.headings h5',
                    'attributes': ['color', 'font-size', 'line-height', 'margin-bottom', 'font-family', 'font-weight', 'font-style']
                },
                {
                    'selector': '.headings h6',
                    'attributes': ['color', 'font-size', 'line-height', 'margin-bottom', 'font-family', 'font-weight', 'font-style']
                },
                {
                    'selector': '.sg-button .button',
                    'attributes': ['background-color', 'background-image', 'background-repeat', 'background-position', 'color', 'font-size', 'line-height', 'height', 'padding', 'width']
                }
            ];

            // Overrides the default settings
            ba.overrideSettings(this.settings, options);

            // Start the debugger
            ba.setupDebugging(this.settings);

            // Add CSS Styling Markup Preview for All Applicable Elements
            this.setupCss();

            // Add DOM Markup Preview for All Applicable Elements
            this.setupMarkup();

            // Run Syntax Highlighting Script for Applicable Elements
            Prism.highlightAll();
        },

        setupCss: function(){
            var self = this;

            $.each(self.css, function(idx, css){
                self.setCodeCss(css.selector, css.attributes);
            });
        },

        getProperties: function(selector, attributes) {
            var self = this, propertyString = '';

            propertyString += ($(selector).prop('tagName').toLowerCase() + self.getElementClassListString($(selector)) + ' {\n');

            $.each(attributes, function(idx, prop){
                if(prop === 'color' || prop === 'background-color' || prop === 'border-color') {
                    propertyString += '    ' + prop + ': ' + (self.getHexValue($(selector).css(prop))) + ';\n';
                }else{
                    propertyString += '    ' + prop + ': ' + ($(selector).css(prop))+ ';\n';
                }
            });

            propertyString += '}';

            return propertyString;
        },

        getElementClassListString: function(selector) {
            var self = this,
                classList = '';

            $.each($(selector).prop('classList'), function(idx, className){
               classList += '.' + className;
            });

            return classList;
        },

        hexCalculation: function(value) {
            var hexDigits = new Array ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");

            return isNaN(value) ? "00" : hexDigits[(value - value % 16) / 16] + hexDigits[value % 16];
        },

        getHexValue: function(rgbvalue) {
            var self = this,
                rgb = rgbvalue.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);

            return "#" + self.hexCalculation(rgb[1]) + self.hexCalculation(rgb[2]) + self.hexCalculation(rgb[3]);
        },

        setCodeCss: function(selector, attributes) {
            var self = this;

            $.each($(selector), function(idx, currentElement){
                var propertyString = self.getProperties(currentElement, attributes);

                $(currentElement).parent('.source').next('.code').append('<pre><code class="language-css">' + propertyString + '</code></pre>');
            });
        },

        setupMarkup: function() {
            var self = this;

            // Standard Typography Pieces & Headings
            $.each($('.source'), function(idx){
                var newString = ba.escapeHtmlString($(this).html());
                $(this).next('.code').find('code').append(newString);
            });

        },

    };

    /**
     * The parameter object is optional.
     * Must be an object.
     */
    if($('.cms-style-guide').length > 0) {
        ba.StyleGuideColor = new StyleGuideColor({});
    }

});