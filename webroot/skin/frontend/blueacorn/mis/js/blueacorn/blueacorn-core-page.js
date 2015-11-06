/**
 * @package     Blueacorn/Page
 * @version     1.0
 * @author      Blue Acorn <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn.
 */

function Page(options) {
    this.init(options);
}

(function($){

    Page.prototype = {
        init: function (options) {
            this.settings = {
                'moduleName': 'Page',
                'bodyTag': $('body'),
            };

            this.pageTypes = [
                {
                    pageName: 'home',
                    bodyClass: ['cms-index-index', 'cms-home'],
                    pageElement: []
                },
                {
                    pageName: 'cms',
                    bodyClass: ['cms-page-view'],
                    pageElement: []
                },
                {
                    pageName: 'category',
                    bodyClass: ['catalog-category-view', 'catalog-category-default', 'catalog-category-layered', 'catalogsearch-result-index', 'catalogsearch-advanced-index', 'catalogsearch-advanced-result', 'catalogsearch-term-popular'],
                    pageElement: []
                },
                {
                    pageName: 'search',
                    bodyClass: ['catalogsearch-result-index', 'catalogsearch-advanced-index', 'catalogsearch-advanced-result', 'catalogsearch-term-popular'],
                    pageElement: []
                },
                {
                    pageName: 'product',
                    bodyClass: ['catalog-product-view'],
                    pageElement: []
                },
                {
                    pageName: 'cart',
                    bodyClass: ['checkout-cart-index'],
                    pageElement: []
                },
                {
                    pageName: 'checkout',
                    bodyClass: ['checkout-onepage-index'],
                    pageElement: []
                },
                {
                    pageName: 'checkout-success',
                    bodyClass: ['checkout-onepage-success'],
                    pageElement: []
                },
                {
                    pageName: 'private-sales',
                    bodyClass: ['restruction-privatesales-mode', 'restruction-index-stub'],
                    pageElement: []
                },
                {
                    pageName: 'my-account',
                    bodyClass: ['customer-account-create','customer-account-login', 'customer-account-logoutsuccess'],
                    pageElement: ['.sidebar .block-account', '.col-main .my-account']
                }
            ];

            // Overrides the default settings
            ba.overrideSettings(this.settings, options);

            // Start the debugger
            ba.setupDebugging(this.settings);
        },
        getPage: function(pageType) {
            var self = this,
                pageRequest = false;

            $.each(self.pageTypes, function(idx, pageObj){
                if(pageObj.pageName === pageType && !pageRequest){

                    $.each(pageObj.bodyClass, function(idx, bodyClass){

                        if(self.settings.bodyTag.hasClass(bodyClass)){
                            pageRequest = true;
                            return;
                        }
                    });

                    if(!pageRequest) {
                        $.each(pageObj.pageElement, function(idx, pageElement){
                            if($(pageElement).length > 0) {
                                pageRequest = true;
                                return;
                            }
                        });
                    }
                }
            });

            return pageRequest;
        },
        identifyPage: function() {
            var self = this,
                pageRequest;

            $.each(self.pageTypes, function(idx, pageType){
                if(self.getPage(pageType.pageName)){

                    pageRequest = pageType.pageName;
                    return;
                }
            });

            return pageRequest;
        },
    };

    /**
     * The parameter object is optional.
     * Must be an object.
     */
    ba.Page = new Page({});

})(jQuery);