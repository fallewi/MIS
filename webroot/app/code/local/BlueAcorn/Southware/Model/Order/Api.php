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

            $attributeTextArray[$item->getSku()] = $product->getAttributeText('uom');
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
            $stockTextArray[$item->getSku()] = $product->getAttributeText('stock');
        }
        return $stockTextArray;
    }
}