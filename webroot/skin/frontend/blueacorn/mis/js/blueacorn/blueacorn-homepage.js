function BlueAcornHomepage() { this.init(); }

jQuery(document).ready(function($){

    BlueAcornHomepage.prototype = {
        init: function(){

            var self = this;

            self.setupObservers();
        },

        setupObservers: function(){

            var self = this;

            self.minHeightFeaturedProducts();

            $(window).resize(function(){
                self.minHeightFeaturedProducts();
            });
        },

        // Set a minimum height for all featured products blocks based on the largest item
        minHeightFeaturedProducts: function(){
            
            var self = this;

            self.minHeight = 426;

            $('.feature-item').css('min-height', "");

            $('.feature-item').each(function(){
                var itemHeight = $(this).outerHeight();

                if(itemHeight > self.minHeight){
                    self.minHeight = itemHeight;
                }
            });

            $('.feature-item').css('min-height', self.minHeight);
        }
    };

    if($('body').hasClass('cms-index-index')){
        ba.Homepage = new BlueAcornHomepage();
    }
});