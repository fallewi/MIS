<?php

class BlueAcorn_Legacy_Model_Resource_Order_Item_Collection extends Mage_Sales_Model_Resource_Order_Item_Collection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'blueacorn_legacy_order_item_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'order_item_collection';

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField   = 'order_id';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('blueacorn_legacy/order_item');
    }
}
