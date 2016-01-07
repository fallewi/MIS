<?php

class BlueAcorn_Legacy_Model_Order_Address extends Mage_Sales_Model_Order_Address
{
    protected $_order;

    protected $_eventPrefix = 'blueacorn_legacy_sales_order_address';
    protected $_eventObject = 'address';

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('blueacorn_legacy/order_address');
    }

    /**
     * Get order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = Mage::getModel('blueacorn_legacy/order')->load($this->getParentId());
        }
        return $this->_order;
    }
}