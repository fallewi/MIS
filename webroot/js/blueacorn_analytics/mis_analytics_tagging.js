(function($) {
    // Data from the CSV generated by Node script (array of objects)
    var data = [{"trackType":"pageViewOnLoad","el":null,"page":"/Category","type":null,"label":null,"eventType":"click","bodyClass":"catalog-category-view","row":2},{"trackType":"pageViewOnLoad","el":null,"page":"/Product","type":null,"label":null,"eventType":"click","bodyClass":"catalog-product-view","row":3},{"trackType":"pageViewOnLoad","el":null,"page":"/Search","type":null,"label":null,"eventType":"click","bodyClass":"catalogsearch-result-index","row":4},{"trackType":"pageViewOnLoad","el":null,"page":"/Cart","type":null,"label":null,"eventType":"click","bodyClass":"checkout-cart-index","row":5},{"trackType":"event","el":".catalog-category-view .toolbar .pages a.next:first","page":"Category Page","type":"Page Right - Upper","label":null,"eventType":"mousedown","bodyClass":null,"row":6},{"trackType":"event","el":".catalog-category-view .toolbar .pages a.previous:first","page":"Category Page","type":"Page Left - Upper","label":null,"eventType":"mousedown","bodyClass":null,"row":7},{"trackType":"event","el":".catalog-category-view .toolbar .pages a.previous:last","page":"Category Page","type":"Page Left - Lower","label":null,"eventType":"mousedown","bodyClass":null,"row":8},{"trackType":"event","el":".catalog-category-view .toolbar .pages a.next:last","page":"Category Page","type":"Page Right - Lower","label":null,"eventType":"mousedown","bodyClass":null,"row":9},{"trackType":"event","el":".page .page-header .page-header-container .logo","page":"Header","type":"Logo Home","label":null,"eventType":"mousedown","bodyClass":null,"row":10},{"trackType":"event","el":".page .page-header .page-header-container #header-search .search-button","page":"Header","type":"Search","label":null,"eventType":"mousedown","bodyClass":null,"row":11},{"trackType":"event","el":".page .page-header .page-header-container .account-cart-wrapper .skip-account","page":"Header","type":"My Account","label":null,"eventType":"mousedown","bodyClass":null,"row":12},{"trackType":"event","el":".page .page-header .page-header-container .account-cart-wrapper .header-minicart .skip-cart","page":"Header","type":"Mini Cart","label":null,"eventType":"mousedown","bodyClass":null,"row":13},{"trackType":"event","el":".page .page-header .page-header-container .account-cart-wrapper .header-minicart #header-cart .minicart-wrapper .minicart-actions .cart-link","page":"Mini Cart","type":"Cart","label":null,"eventType":"mousedown","bodyClass":null,"row":14},{"trackType":"event","el":".page .page-header .page-header-container .account-cart-wrapper .header-minicart #header-cart .minicart-wrapper .minicart-actions .checkout-types.minicart .checkout-button","page":"Mini Cart","type":"Checkout","label":null,"eventType":"mousedown","bodyClass":null,"row":15},{"trackType":"event","el":".checkout-cart-index .cart .title-buttons .checkout-types.top .btn-proceed-checkout","page":"Cart","type":"Top Proceed to Checkout","label":null,"eventType":"mousedown","bodyClass":null,"row":16},{"trackType":"event","el":".checkout-cart-index .cart .cart-totals-wrapper .cart-totals .checkout-types.bottom .btn-proceed-checkout","page":"Cart","type":"Lower Proceed to Checkout","label":null,"eventType":"mousedown","bodyClass":null,"row":17},{"trackType":"event","el":".checkout-cart-index .cart .cart-forms .discount .discount-form .field-wrapper .button-wrapper .button2","page":"Cart","type":"Promo Code Submit","label":null,"eventType":"mousedown","bodyClass":null,"row":18},{"trackType":"event","el":".checkout-cart-index .cart .cart-forms .giftcard #giftcard-form .field-wrapper .button-wrapper .button2","page":"Cart","type":"Gift Card Code Submit","label":null,"eventType":"mousedown","bodyClass":null,"row":19},{"trackType":"event","el":".checkout-cart-index .cart .cart-forms .shipping .shipping-form #shipping-zip-form .buttons-set .button2","page":"Cart","type":"Tax & Shipping Estimator Submit","label":null,"eventType":"mousedown","bodyClass":null,"row":20},{"trackType":"event","el":".checkout-cart-index .cart .cart-forms .shipping .shipping-form .form-list","page":"Cart","type":"Tax & Shipping Estimator","label":null,"eventType":"mousedown","bodyClass":null,"row":21},{"trackType":"event","el":".checkout-cart-index .cart .cart-table .cart-footer-actions .btn-continue","page":"Cart","type":"Cart Continue Shopping","label":null,"eventType":"mousedown","bodyClass":null,"row":22},{"trackType":"event","el":".checkout-cart-index .cart .cart-table .cart-footer-actions .btn-update","page":"Cart","type":"Update Cart","label":null,"eventType":"mousedown","bodyClass":null,"row":23},{"trackType":"pageView","el":".checkout-onepage-index .opc #opc-login #checkout-step-login .col-1 .form-list .control .radio-label:first","page":"/Checkout as Guest","type":null,"label":null,"eventType":"mousedown","bodyClass":null,"row":24},{"trackType":"pageView","el":".checkout-onepage-index .opc #opc-login #checkout-step-login .col-1 .form-list .control .radio-label:last","page":"/Register & Checkout","type":null,"label":null,"eventType":"mousedown","bodyClass":null,"row":25},{"trackType":"pageView","el":".checkout-onepage-index .opc #opc-login #checkout-step-login .col-2 .buttons-set .button","page":"/Checkout Log In","type":null,"label":null,"eventType":"mousedown","bodyClass":null,"row":26},{"trackType":"pageView","el":".checkout-onepage-index .opc #opc-login #checkout-step-login .col-1 .buttons-set .button","page":"/Continue to Billing Info (From Checkout Method)","type":null,"label":null,"eventType":"mousedown","bodyClass":null,"row":27},{"trackType":"pageView","el":".opc #opc-billing #checkout-step-billing #co-billing-form .fieldset #billing-buttons-container .continue","page":"/Continue to Shipping Info (from Billing)","type":null,"label":null,"eventType":"mousedown","bodyClass":null,"row":28},{"trackType":"pageView","el":".opc #opc-shipping #checkout-step-shipping #co-shipping-form #shipping-buttons-container .continue","page":"/Continue to Shipping Method (from Shipping Information)","type":null,"label":null,"eventType":"mousedown","bodyClass":null,"row":29},{"trackType":"pageView","el":".opc #opc-shipping_method #checkout-step-shipping_method #co-shipping-method-form #shipping-method-buttons-container .continue","page":"/Continue to Payment Information","type":null,"label":null,"eventType":"mousedown","bodyClass":null,"row":30},{"trackType":"pageView","el":".opc #opc-payment #checkout-step-payment #payment-buttons-container .continue","page":"/Continue to Order Review","type":null,"label":null,"eventType":"mousedown","bodyClass":null,"row":31},{"trackType":"event","el":".page .footer-container .footer .connect-container .block-subscribe .block-content .actions .button","page":"Footer","type":"Email Subscription","label":null,"eventType":"mousedown","bodyClass":null,"row":32}];

    // Instantiate the constructor
    var BA_GAQ = new GaqTracker(data);

    BA_GAQ.setMode('universal');

    // Run the tracking AFTER doc ready
    $(document).ready(function() {

        /* -------------------------- *\
         *     MAIN AUTOTRACKING      *
        \* -------------------------- */
        BA_GAQ.trackAll();

        /* ------------------------- *\
         *      MANUAL TRACKING      *
        \* ------------------------- */

    }); // end doc ready

}(jQuery));
