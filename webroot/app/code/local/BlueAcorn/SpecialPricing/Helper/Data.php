<?php
/**
 * BlueAcorn_SpecialPricing extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       BlueAcorn
 * @package        BlueAcorn_SpecialPricing
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * SpecialPricing default helper
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_SpecialPricing
 * @author      Ultimate Module Creator
 */
class BlueAcorn_SpecialPricing_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     * @author Ultimate Module Creator
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }


    public function canLowestPriceBeDisplayed($product)
    {
        $product = Mage::getModel('catalog/product')->load($product->getId());
        if(Mage::getSingleton('customer/session')->isLoggedIn())
        {
            return true;
        }
        if($mapRequired = $product->getAttributeText('map_required'))
        {
            if($mapRequired == "Disabled")
            {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }
}
