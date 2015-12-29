<?php

class BlueAcorn_Legacy_Model_Resource_Order_Address_Collection extends Mage_Sales_Model_Resource_Order_Address_Collection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'blueacorn_legacy_order_address_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'blueacorn_legacy_order_address_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('blueacorn_legacy/order_address');
    }
}