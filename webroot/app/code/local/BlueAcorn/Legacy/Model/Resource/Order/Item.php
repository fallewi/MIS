<?php

class BlueAcorn_Legacy_Model_Resource_Order_Item extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'blueacorn_legacy_sales_order_item_resource';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('blueacorn_legacy/order_item', 'item_id');
    }
}
