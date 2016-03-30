<?php
/**
 * @package     BlueAcorn\GoogleTrustedStores
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2016 Blue Acorn, Inc
 */

class BlueAcorn_GoogleTrustedStores_Block_Onepage_Success extends Mage_Checkout_Block_Onepage_Success
{
    const GUEST_EMAIL_USER = 'guest';

    /**
     *
     * Constructor function
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        if (!$this->hasData('order_id')) {
            $this->setData('order_id', Mage::getSingleton('checkout/session')->getLastOrderId());
        }
    }

    /**
     *
     * Returns the order id of the current order
     *
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    /**
     * Returns the back order status of order items
     *
     * @param Mage_Sales_Model_Order $order Order.
     * @return string $hasBackOrder
     */
    public function hasBackorderPreorder($order)
    {
        $hasBackOrder = 'N';

        foreach ($order->getAllItems() as $orderItem) {
            $productId = $orderItem->getProductId();
            $product = Mage::getModel("catalog/product")->load($productId);
            $hasBackOrder = $this->_getBackOrderStatus($product);
            if ($hasBackOrder === 'Y') {
                break;
            }
        }
        return $hasBackOrder;
    }

    /**
     * Returns status of an order that has digital goods
     *
     * @return string $hasDigitalGoods
     */
    public function hasDigitalGoods()
    {
        $hasDigitalGoods = 'N';
        $orderId = $this->getOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

        foreach ($order->getAllItems() as $item) {
            $product = $item->getProduct();
            $productType = $product->getTypeId();

            if ($productType == Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL) {
                $hasDigitalGoods = 'Y';
            }
        }

        return $hasDigitalGoods;
    }

    /**
     * Returns currency code
     *
     * @return string
     */
    public function getCurrency()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Return filtered address
     *
     * @param string $email Email to be filtered.
     * @return float|integer|string
     */
    public function filterEmail($email)
    {
        return $this->_format($email, 'email');
    }

    /**
     * Return filtered price
     *
     * @param string $price Price to be filtered.
     * @return float|integer|string
     */
    public function filterPrice($price)
    {
        return $this->_format($price, 'price');
    }

    /**
     * Returns back order status of product
     *
     * @param Mage_Catalog_Model_Product $product Product.
     * @return string
     */
    protected function _getBackOrderStatus($product)
    {
        if ($product->isSaleable() && !$product->getIsInStock()) {
            return 'Y';
        } else {
            return 'N';
        }
    }

    /**
     * Returns a formatted string
     *
     * @param string $stringToFormat Value that will be formatted.
     * @param string $type Value that will be formatted.
     * @return float|integer|string
     */
    protected function _format($stringToFormat, $type)
    {
        switch($type) {
            case 'email':
                if ($stringToFormat == "") {
                    $domain = preg_replace('#^https?://#', '', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
                    $stringToFormat = self::GUEST_EMAIL_USER . '@' . $domain;
                    $stringToFormat = rtrim($stringToFormat, '/');
                }
                break;
            case 'price':
                $stringToFormat = number_format(floatval($stringToFormat), 2);
                if ($stringToFormat == 0) {
                    $stringToFormat = 0;
                }
                break;
        }
        return $stringToFormat;
    }
}
