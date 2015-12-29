<?php

class BlueAcorn_Legacy_Model_Resource_Order extends Mage_Sales_Model_Resource_Order
{
    protected $_eventPrefix = 'sales_order_resource';

    protected function _construct()
    {
        $this->_init('blueacorn_legacy/order', 'entity_id');
    }
}