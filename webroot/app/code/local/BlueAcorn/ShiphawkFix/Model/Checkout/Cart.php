<?php
/**
 * @package     BlueAcorn\ShiphawkFix
 * @version     0.1.0
 * @author      Sam Tay @ Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */
class BlueAcorn_ShiphawkFix_Model_Checkout_Cart extends Shiphawk_Checkout_Model_Cart
{
    /**
     * Fix Shiphawk not returning $this and dispatching the 'before' event twice
     * (Original model does this in the case that calc_rate_on_cart_change is set to Yes)
     *
     * Save cart
     *
     * @return Mage_Checkout_Model_Cart
     */
    public function save($includeRates = false)
    {
        /******** START BA CHANGES *********/
        $calcRateOnCartChange = Mage::helper('shiphawk_shipping')->getCalcRateOnCartChange();

        if($calcRateOnCartChange) {
            $mageCart = get_parent_class(get_parent_class($this));
            return $mageCart::save();
        }
        else {
            Mage::dispatchEvent('checkout_cart_save_before', array('cart'=>$this));
            /******** END BA CHANGES *********/

            //retrieving previous subtotal form cookie
            $previousCartSubTotal = Mage::getSingleton('core/session')->getData('previousCartSubTotal', true);

            $this->getQuote()->getBillingAddress();
            $this->getQuote()->getShippingAddress();
            $this->getQuote()->collectTotals();

            if ($includeRates) {

                $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            } else if ($previousCartSubTotal != $this->getQuote()->getSubtotal()) {

                $this->getQuote()->getShippingAddress()->removeAllShippingRates();
                $this->getQuote()->getShippingAddress()->setShippingMethod('')->setShippingDescription('');
            }

            $this->getQuote()->collectTotals();
            $this->getQuote()->save();
            $this->getCheckoutSession()->setQuoteId($this->getQuote()->getId());
            /**
             * Cart save usually called after changes with cart items.
             */

            Mage::getSingleton('core/session')->setData('previousCartSubTotal', $this->getQuote()->getSubtotal());

            Mage::dispatchEvent('checkout_cart_save_after', array('cart' => $this));
            return $this;
        }
    }
}