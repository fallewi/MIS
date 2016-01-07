<?php

class BlueAcorn_Legacy_Model_Order_Item extends Mage_Sales_Model_Order_Item
{
    protected $_eventPrefix = 'blueacorn_legacy_sales_order_item';
    protected $_eventObject = 'item';

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('blueacorn_legacy/order_item');
    }
}