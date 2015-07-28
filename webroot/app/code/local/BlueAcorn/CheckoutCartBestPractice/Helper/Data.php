<?php
/**
* @package     BlueAcorn\CheckoutCartBestPractice
* @version     0.1.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2015 Blue Acorn, Inc.
*/

class BlueAcorn_CheckoutCartBestPractice_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getCartConfig($field)
    {
        return Mage::getStoreConfig('bestpractices/cart/'. $field);
    }
    public function getCheckoutBaseConfig($field)
    {
        return Mage::getStoreConfig('bestpractices/checkout/' . $field);
    }
    public function getCheckoutConfig($step, $field)
    {
        return Mage::getStoreConfig('bestpractices/checkout/' . $step . DS . $field);
    }
    public function getMageJsConfig($step, $field)
    {
        return Mage::getStoreConfig('bestpractices/'. $step . DS . $field);
    }
    public function getMediaUrl(){
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
    }

    public function getSystemConfig(array $directories) {
        $path = "bestpractices" . DS . implode('/', $directories);
        return Mage::getStoreConfig($path);
    }

    /**
     * Get placeholders for keys without having to worry about undefined indices
     *
     * @param array $keys
     * @return mixed
     */
    public function getPlaceholders($keys = array())
    {
        $placeholders = array();
        $allPlaceholders = $this->getSystemConfig(['checkout', 'placeholders']) ?: array();

        foreach($keys as $key) {
            $placeholders[$key] = isset($allPlaceholders[$key]) ? $allPlaceholders[$key] : '';
        }

        return $placeholders;
    }
}