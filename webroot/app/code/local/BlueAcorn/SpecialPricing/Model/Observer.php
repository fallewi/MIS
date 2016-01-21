<?php

/**
 * @package     BlueAcorn\SpecialPricing
 * @version
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */
class BlueAcorn_SpecialPricing_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Change the quote price to the MSRP price
     * IFF
     * Customer is not logged in
     * AND
     * Map is required (email/phone)
     * AND
     * token is not being redeemed
     *
     * @param Varien_Event_Observer $observer
     * @observes checkout_cart_product_add_after
     */
    public function adjustMapPrice(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        $quoteItem = $observer->getQuoteItem();
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getProduct();
        /** @var array $pricingVisibility */
        $pricingVisibility = $this->_getPricingVisibility();

        // Check if we have already unlocked pricing for this product/session
        if (!empty($pricingVisibility[$product->getId()])) {
            return;
        }

        // Gather variables to determine setting or unsetting MSRP price on quote item
        $loggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
        $msrp = $product->getMsrp();
        $mapRequired = $product->getMapRequired();
        $tokenId = Mage::app()->getRequest()->getParam('token');
        $token = $tokenId
            ? Mage::getModel('blueacorn_specialpricing/token')->load($tokenId, 'token')->getId()
            : null;

        if (!$loggedIn && $msrp && $mapRequired && !$token) {
            $quoteItem->setOriginalCustomPrice($msrp);
            $this->_savePricingVisibility($product->getId(), false);
        } else {
            $quoteItem->setOriginalCustomPrice(null);
            $this->_savePricingVisibility($product->getId(), true);
        }
    }

    /**
     * Unset all custom MAP pricing when customer logs in
     *
     * @observes customer_login
     * @param Varien_Event_Observer $observer
     */
    public function unsetMapPrices(Varien_Event_Observer $observer)
    {
        foreach($this->_getPricingVisibility() as $productId => $visible) {
            if (!$visible) {
                $quoteItem = Mage::getSingleton('checkout/session')->getQuote()->getItemByProduct(
                    new Varien_Object(array('id' => $productId))
                );
                if ($quoteItem) {
                    $quoteItem->setOriginalCustomPrice(null);
                    Mage::getSingleton('checkout/session')->getQuote()
                        ->setTotalsCollectedFlag(false)
                        ->collectTotals()
                        ->save();
                }
                $this->_savePricingVisibility($productId, true);
            }
        }
    }

    /**
     * Get pricing visibility key from checkout session
     *
     * @return array
     */
    protected function _getPricingVisibility()
    {
        return Mage::getSingleton('checkout/session')->getPricingVisibility() ?: array();
    }

    /**
     * Save visibility per product on checkout session
     *
     * @param $productId
     * @param $flag
     */
    protected function _savePricingVisibility($productId, $flag)
    {
        $pricingVisibility = $this->_getPricingVisibility();
        $pricingVisibility[$productId] = $flag;

        Mage::getSingleton('checkout/session')->setPricingVisibility($pricingVisibility);
    }
}
