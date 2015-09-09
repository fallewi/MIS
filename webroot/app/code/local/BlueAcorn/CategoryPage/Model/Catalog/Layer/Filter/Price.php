<?php
/**
 * @package     BlueAcorn\CategoryPage
 * @version     
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */ 
class BlueAcorn_CategoryPage_Model_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
{
    /**
     * Rewrite of Layer_Filter_Price label display
     * Prepare text of range label
     *
     * @param float|string $fromPrice
     * @param float|string $toPrice
     * @return string
     */
    protected function _renderRangeLabel($fromPrice, $toPrice)
    {
        $store      = Mage::app()->getStore();

        //This portion was added to prevent format price from inputting symbols
        $formattedFromPrice  = '';
        if (Mage::getModel('directory/currency')) {
            $formattedFromPrice = Mage::getModel('directory/currency')->format($fromPrice, array('display'=>Zend_Currency::NO_SYMBOL), true);
        } else {
            $formattedFromPrice = $fromPrice;
        }

        if ($toPrice === '') {
            return Mage::helper('catalog')->__('%s and above', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice && Mage::app()->getStore()->getConfig(self::XML_PATH_ONE_PRICE_INTERVAL)) {
            return $formattedFromPrice;
        } else {
            if ($fromPrice != $toPrice) {
                $toPrice -= .01;
            }

            //This portion was added to prevent format price from inputting symbols
            $formattedToPrice  = '';
            if (Mage::getModel('directory/currency')) {
                $formattedToPrice = Mage::getModel('directory/currency')->format($toPrice, array('display'=>Zend_Currency::NO_SYMBOL), true);
            } else {
                $formattedToPrice = $toPrice;
            }


            return Mage::helper('catalog')->__('%s - %s', $formattedFromPrice, $formattedToPrice);
        }
    }
}