/**
 * @package     BlueAcorn\\BlueAcornGridPreview
 * @version     1.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2014 Blue Acorn, Inc.
 */

var baGrid;

function BlueAcornGridPreview(options){
    if(options){
        this.options = options;
    }else{
        this.options = '';
    }
    this.init();
}

jQuery(document).ready(function($){

    var debug = false;

    BlueAcornGridPreview.prototype.init = function(){
        this.gridVisible = false;
        this.setupObserver();
    };

    BlueAcornGridPreview.prototype.setupObserver = function(){
        this.setupGrid();

        $(window).on('keypress', function(event){
            var targetTag = event.target.tagName.toLowerCase();
            if(event.keyCode == 103 && targetTag != 'input' && targetTag != 'textarea'){
                if(this.gridVisible){
                    this.hideGrid();
                }else{
                    this.showGrid();
                }
            }
        }.bind(this));
    };

    BlueAcornGridPreview.prototype.setupGrid = function(){
        this.gridTemplate = [
        '<div id="grid">',
            '<div class="mobile"><div class="m-1">m-1</div><div class="m-2">m-2</div><div class="m-3">m-3</div><div class="m-4">m-4</div><div class="m-5">m-5</div><div class="m-6">m-6</div></div>',
            '<div class="tablet"><div class="t-1">t-1</div><div class="t-2">t-2</div><div class="t-3">t-3</div><div class="t-4">t-4</div><div class="t-5">t-5</div><div class="t-6">t-6</div><div class="t-7">t-7</div><div class="t-8">t-8</div></div>',
            '<div class="desktop"><div class="d-1">d-1</div><div class="d-2">d-2</div><div class="d-3">d-3</div><div class="d-4">d-4</div><div class="d-5">d-5</div><div class="d-6">d-6</div><div class="d-7">d-7</div><div class="d-8">d-8</div></div>',
        '</div>'
        ].join('');
    };

    BlueAcornGridPreview.prototype.showGrid = function(){
        this.gridVisible = true;
        $('.page-header').before(this.gridTemplate);
    };

    BlueAcornGridPreview.prototype.hideGrid = function(){
        $('#grid').remove();
        this.gridVisible = false;
    };

    baGrid = new BlueAcornGridPreview();

});

