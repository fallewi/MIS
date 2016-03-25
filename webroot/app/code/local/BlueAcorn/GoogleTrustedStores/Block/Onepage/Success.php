<?php
/**
 * @package     BlueAcorn\GoogleTrustedStores
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2016 Blue Acorn, Inc
 */

class BlueAcorn_GoogleTrustedStores_Block_Onepage_Success extends Mage_Checkout_Block_Onepage_Success
{
    protected function _construct()
    {
        parent::_construct();

        if(!$this->hasData('order_id')) {
            $this->setData('order_id', Mage::getSingleton('checkout/session')->getLastOrderId());
        }
    }

    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    public function hasBackorderPreorder($order)
    {
        $hasBackOrder = 'N';

        foreach($order->getAllItems() as $orderItem)
        {
            $productId = $orderItem->getProductId();
            $product = Mage::getModel("catalog/product")->load($productId);
            $hasBackOrder = $this->_getBackOrderStatus($product);
            if($hasBackOrder === 'Y') {
                break;
            }
        }
        return $hasBackOrder;
    }

    public function hasDigitalGoods()
    {
        $hasDigitalGoods = 'N';
        $orderId = $this->getOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

        foreach($order->getAllItems() as $item) {
            $product = $item->getProduct();
            $productType = $product->getTypeId();

            if($productType == Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL) {
                $hasDigitalGoods = 'Y';
            }
        }

        return $hasDigitalGoods;
    }

    public function getCurrency()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    protected function _getBackOrderStatus($product)
    {
        if($product->isSaleable() && !$product->getIsInStock()) {
            return 'Y';
        } else {
            return 'N';
        }
    }
}
