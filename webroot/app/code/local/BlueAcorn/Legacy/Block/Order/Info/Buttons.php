<?php

class BlueAcorn_Legacy_Block_Order_Info_Buttons extends Mage_Sales_Block_Order_Info_Buttons
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/info/buttons.phtml');
    }

    /**
     * Get url for printing order
     *
     * @param BlueAcorn_Legacy_Model_Order $order
     * @return string
     */
    public function getPrintUrl($order)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this->getUrl('legacy/guest/print', array('order_id' => $order->getId()));
        }
        return $this->getUrl('legacy/order/print', array('order_id' => $order->getId()));
    }
}
