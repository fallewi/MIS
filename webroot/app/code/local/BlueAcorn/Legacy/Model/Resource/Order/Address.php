<?php

class BlueAcorn_Legacy_Model_Resource_Order_Address extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'blueacorn_legacy_sales_order_address_resource';

    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('blueacorn_legacy/order_address', 'entity_id');
    }
}
