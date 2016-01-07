<?php

class BlueAcorn_Legacy_Block_Order_View extends Mage_Sales_Block_Order_View
{
    public function getBackUrl()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::getUrl('sales/*/history');
        }
        return Mage::getUrl('*/*/form');
    }
}