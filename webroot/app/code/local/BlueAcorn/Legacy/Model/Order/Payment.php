<?php

class BlueAcorn_Legacy_Model_Order_Payment extends Mage_Sales_Model_Order_Payment
{
    protected $_eventPrefix = 'blueacorn_legacy_sales_order_payment';

    protected function _construct()
    {
        $this->_init('blueacorn_legacy/order_payment');
    }

    public function getMethodInstance()
    {
        if (!$this->hasMethodInstance()) {
            $method = $this->getMethod();
            if ($method) {
                if ($method === 'cybersource_soap') {
                    $instance = Mage::helper('payment')->getMethodInstance('ccsave');
                } else {
                    $instance = Mage::helper('payment')->getMethodInstance($this->getMethod());
                }

                if ($instance) {
                    $instance->setInfoInstance($this);
                    $this->setMethodInstance($instance);
                    return $instance;
                }
            }
            Mage::throwException(Mage::helper('payment')->__('The requested Payment Method is not available.'));
        }

        return $this->_getData('method_instance');
    }
}