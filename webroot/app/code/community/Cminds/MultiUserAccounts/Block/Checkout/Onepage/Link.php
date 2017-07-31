<?php

class Cminds_MultiUserAccounts_Block_Checkout_Onepage_Link extends Mage_Checkout_Block_Onepage_Link
{
    public function cartIsApproved()
    {
        $quoteId = Mage::getSingleton('checkout/session')->getQuote()->getId();
        return Mage::helper('cminds_multiuseraccounts')->isQuoteApproved($quoteId);
    }

    public function hasCreateOrderPermission()
    {
        if (Mage::helper('cminds_multiuseraccounts')->hasNeedApprovalPermission()) {
            $quoteId = Mage::getSingleton('checkout/session')->getQuote()->getId();
            return Mage::helper('cminds_multiuseraccounts')->isQuoteApproved($quoteId);
        }
        return Mage::helper('cminds_multiuseraccounts')->hasCreateOrderPermission();
    }
}