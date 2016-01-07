<?php

class BlueAcorn_Legacy_Model_Resource_Order_Payment extends Mage_Sales_Model_Resource_Order_Payment
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'blueacorn_legacy_sales_order_payment_resource';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('blueacorn_legacy/order_payment', 'entity_id');
    }
}
