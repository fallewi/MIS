<?php
/**
* @package     BlueAcorn\CheckoutCartBestPractice
* @version     0.1.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2015 Blue Acorn, Inc.
*/

class BlueAcorn_CheckoutCartBestPractice_Block_Checkout_Onepage_Success extends Mage_Checkout_Block_Onepage_Success {
    private $order = null;

    public function setOrder($order_id){
        $this->order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
    }

    public function getOrder(){
        if (is_null($this->order)) {
            $order_id = $this->getOrderId();
            $this->setOrder($order_id);
        }
        return $this->order;
    }

    public function getBillingAddress() {
        $billing_address = $this->getOrder()->getBillingAddress();
        return $billing_address;
    }

    public function getShippingAddress(){
        $shipping_address = $this->getOrder()->getShippingAddress();
        return $shipping_address;
    }

    public function toCurrency($amount){
        return Mage::helper('core')->currency($amount, true, false);
    }
}