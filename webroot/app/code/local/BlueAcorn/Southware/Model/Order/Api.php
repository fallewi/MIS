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

    /**
     * Override of Mage_Sales_Model_Order_Api::info()
     *
     * @param string $orderIncrementId
     * @return Mage_Sales_Model_Api_Resource
     */
    public function info($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        if ($order->getGiftMessageId() > 0) {
            $order->setGiftMessage(
                Mage::getSingleton('giftmessage/message')->load($order->getGiftMessageId())->getMessage()
            );
        }

        $result = $this->_getAttributes($order, 'order');

        $result['shipping_address'] = $this->_getAttributes($order->getShippingAddress(), 'order_address');
        $result['billing_address']  = $this->_getAttributes($order->getBillingAddress(), 'order_address');
        $result['items'] = array();

        foreach ($order->getAllItems() as $item) {
            if ($item->getGiftMessageId() > 0) {
                $item->setGiftMessage(
                    Mage::getSingleton('giftmessage/message')->load($item->getGiftMessageId())->getMessage()
                );
            }
            // BlueAcorn rewrite, adding attrubutes 'UOM' and 'Stock'
            $attributes = array_merge($this->_getAttributes($item, 'order_item'), $this->getUom($orderIncrementId),$this->getStock($orderIncrementId));
            $result['items'][] = $attributes;
        }
        // BlueAcorn rewrite adding customer comments
        $result['customer_comments'] = $this->getCustomerComments($orderIncrementId);
        $result['payment'] = $this->_getAttributes($order->getPayment(), 'order_payment');

        $result['status_history'] = array();

        foreach ($order->getAllStatusHistory() as $history) {
            $result['status_history'][] = $this->_getAttributes($history, 'order_status_history');
        }

        return $result;
    }

    /**
     * Returns Customer Comments on orders
     *
     * @param $orderNumber
     * @return string
     */
    public function getCustomerComments($orderNumber)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
        $orderId = $order->getId();

        $customerComment = Mage::getResourceModel('sales/order')->getCustomerComments($orderId);
        return $customerComment;
    }

    /**
     * Returns an array of product's unit of measure attribute
     *
     * @param $orderNumber
     * @return array
     */
    public function getUom($orderNumber)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
        $attributeTextArray = array();

        foreach($order->getAllVisibleItems() as $item)
        {
            $attributeOptionId = Mage::getResourceModel('catalog/product')
                ->getAttributeRawValue($item->getProductId(), 'uom', $item->getStoreId());

            $product = Mage::getModel('catalog/product')
                ->setStoreId($item->getStoreId())
                ->setData('uom', $attributeOptionId);

            $attributeTextArray['UOM'] = $product->getAttributeText('uom');
        }
            return $attributeTextArray;
    }

    /**
     * Returns product's stock attribute
     *
     * @param $orderNumber
     * @return array
     */
    public function getStock($orderNumber)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
        $stockTextArray = array();

        foreach($order->getAllVisibleItems() as $item)
        {
            $attributeOptionId = Mage::getResourceModel('catalog/product')
                ->getAttributeRawValue($item->getProductId(), 'stock', $item->getStoreId());

            $product = Mage::getModel('catalog/product')
                ->setStoreId($item->getStoreId())
                ->setData('stock', $attributeOptionId);
            $stockTextArray['Stock'] = $product->getAttributeText('stock');
        }
        return $stockTextArray;
    }
}