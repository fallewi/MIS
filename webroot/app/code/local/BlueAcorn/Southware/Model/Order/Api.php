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
            $attributes = array_merge($this->_getAttributes($item, 'order_item'), $this->getUom($item),$this->getStock($item));
            $result['items'][] = $attributes;
        }
        // BlueAcorn rewrite adding customer comments and SW Customer ID
        $result['customer_comments'] = $this->getCustomerComments($orderIncrementId);
        $result['sw_customer_id'] = $this->getSwCustomerId($order->getCustomerId());

        $result['payment'] = $this->_getAttributes($order->getPayment(), 'order_payment');

        $result['status_history'] = array();

        foreach ($order->getAllStatusHistory() as $history) {
            $result['status_history'][] = $this->_getAttributes($history, 'order_status_history');
        }

        /** looping data to capitalize the string values */
        foreach ($result as $key => $value) {
            $result[$key] = $this->capitalize($value, $key);
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
     * @param $item
     * @return array
     */
    public function getUom($item)
    {
        $attributeOptionId = Mage::getResourceModel('catalog/product')
            ->getAttributeRawValue($item->getProductId(), 'uom', $item->getStoreId());

        $product = Mage::getModel('catalog/product')
            ->setStoreId($item->getStoreId())
            ->setData('uom', $attributeOptionId);

        $attributeTextArray['uom'] = $product->getAttributeText('uom');

        return $attributeTextArray;
    }

    /**
     * Returns product's stock attribute
     *
     * @param $item
     * @return array
     */
    public function getStock($item)
    {
        $attributeOptionId = Mage::getResourceModel('catalog/product')
            ->getAttributeRawValue($item->getProductId(), 'stock', $item->getStoreId());

        $product = Mage::getModel('catalog/product')
            ->setStoreId($item->getStoreId())
            ->setData('stock', $attributeOptionId);
        $stockTextArray['stock'] = $product->getAttributeText('stock');

        return $stockTextArray;
    }

    /**
     * Returns customer's SouthWare ID if customer has one
     *
     * @param $customerId
     * @return string
     */
    public function getSwCustomerId($customerId)
    {
        $swCustomerId = "";

        if($customerId){
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $swCustomerId = $customer->getSouthwareCustomerId();
        }
        if($swCustomerId === null){
            $swCustomerId = "";
        }

        return $swCustomerId;
    }

    /**
     * Capitalizing string values and formats phone numbers to xxx-xxx-xxxx
     *
     * @param $item
     * @param null $placeholder
     * @return array|string
     */
    public function capitalize($item, $placeholder = null) {
        if(is_array($item)) {
            foreach ($item as $key => $value) {
                $item[$key] = $this->capitalize($value, $key);
            }
            return $item;
        }

        if($placeholder === 'telephone') {
            /** formatting phone number to be xxx-xxx-xxxx */
            $formatted_number = preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $item);
            $item = $formatted_number;
        }

        if(is_string($item)) {
            $data = preg_match('/^[ao][:]/', $item);
            if ($data == 0) {
                return strtoupper($item);
            }
        }

        return $item;
    }
}