/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (http://www.amasty.com)
 * @package Amasty_Orderattr
 */

var amFileUploader = new Class.create();
amFileUploader.prototype = {
    options: null,

    initialize: function (options) {
        this.options = options;
        var fileInput = $$('input[name="amorderattr[' +  this.options['name']  + ']"]').first();
        if ( fileInput && this.options['extension'] != "" ) {
            var extensions = this.options['extension'];
            extensions = extensions.split(',');
            extensions = '.' + extensions.join(',.');
            fileInput.setAttribute('accept', extensions);
        }
    },

    sendFileWithAjax: function (input) {
        var formData = new FormData();
        var files = input.files;
        var name = $(input).getAttribute('name');
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            formData.append(name, file, file.name);
        }

        if ('undefined' != typeof(FORM_KEY)) {
            formData.append('form_key',FORM_KEY);
        }

        for (key in this.options) {
            formData.append(key, this.options[key]);
        }

        var xhr = new XMLHttpRequest();
        xhr.element = $(input);
        xhr.open('POST',  this.options['url'], true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                $$('.amorderattr-error').each(function(block){
                    block.remove();
                });
                if(xhr.response) {
                    var json = JSON.parse(xhr.response);
                    if(json && json.error) {
                        var errorContainer = new Element('div', {
                            'class' : 'amorderattr-error',
                            'style' : 'color: red; display: none;'
                        });
                        errorContainer.insert('<span>' + json.error + '</span>');
                        xhr.element.parentNode.insertBefore(errorContainer, xhr.element.nextSibling);
                        Effect.SlideDown(errorContainer);
                        return true;
                    }
                }
            } else {
                alert('An error occurred!');
            }
        };

        xhr.send(formData);
    }
}


