<?php

class BlueAcorn_Legacy_Model_Resource_Order_Payment_Collection extends Mage_Sales_Model_Resource_Order_Payment_Collection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'blueacorn_legacy_sales_order_payment_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'order_payment_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('blueacorn_legacy/order_payment');
    }

    /**
     * Unserialize additional_information in each item
     *
     * @return Mage_Sales_Model_Resource_Order_Payment_Collection
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }
        return parent::_afterLoad();
    }
}
