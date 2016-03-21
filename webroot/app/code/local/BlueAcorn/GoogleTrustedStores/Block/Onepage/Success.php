<?php
/**
 * @package     BlueAcorn\GoogleTrustedStores
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2016 Blue Acorn, Inc.
 */

class BlueAcorn_GoogleTrustedStores_Block_Onepage_Success extends Mage_Checkout_Block_Onepage_Success {

    protected function _construct()
    {
        parent::_construct();

        if(!$this->hasData('order_id')) {
            $this->setData('order_id', Mage::getSingleton('checkout/session')->getLastOrderId());
        }
    }

    public function getOrderId() {
        return $this->getData('order_id');
    }

    public function hasBackorderPreorder($order)
    {
        $hasBackorderPreorder = 'N';

        foreach($order->getAllItems() as $orderItem)
        {
            $productId = $orderItem->getProductId();
            $product = Mage::getModel("catalog/product")->load($productId);
            $productType = $product->getTypeId();

            switch($productType) {
                case 'simple':
                    if($product->isSaleable() && !$product->getIsInStock()) {
                        $hasBackorderPreorder = 'Y';
                    }
                    break;
                case 'configurable':
                    $childProducts = $product->getTypeInstance(true)->getUsedProducts(null,$product);
                    foreach($childProducts as $childProduct) {
                        if($childProduct->isSaleable() && !$childProduct->getIsInStock()) {
                            $hasBackorderPreorder = 'Y';
                        }
                    }
                    break;
                default:
                    if($product->isSaleable() && !$product->getIsInStock()) {
                        $hasBackorderPreorder = 'Y';
                    }
            }
        }
        return $hasBackorderPreorder;
    }

    public function hasDigitalGoods() {
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

    public function getCurrency() {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

}
