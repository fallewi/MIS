<?php
/**
 * @package BlueAcorn_SouthwareApi
 * @version 1.0.0
 * @author BlueAcorn
 * @copyright Copyright (c) 2015 Blue Acorn, Inc.
 */

class BlueAcorn_Southware_Model_Order_Api extends Mage_Sales_Model_Order_Api
{
    /**
     * Sets Southware Order ID on Order provided by Southware
     *
     * @param $orderNumber
     * @param $southwareOrderId
     */
    public function setSouthwareOrderId($orderNumber, $southwareOrderId)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
        $orderId = $order->getId();

        Mage::getResourceModel('sales/order')->setSouthWareOrderId($orderId, $southwareOrderId);
    }
}