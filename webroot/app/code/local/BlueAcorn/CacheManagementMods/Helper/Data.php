<?php
/**
 * @package     BlueAcorn\CacheManagementMods
 * @version     
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */ 
class BlueAcorn_CacheManagementMods_Helper_Data extends Mage_Core_Helper_Abstract {


    public function getButtonList()
    {
        $button = Mage::getStoreConfig('blueacorn_cachemanagementmods/general/commands');
        return unserialize($button);

    }

    public function sanitizeLabel($label)
    {
        $trimmed = trim($label); // Trims both ends
        $trimmed = strtolower($trimmed);
        $convert = str_replace(' ', '_', $trimmed);
        return $convert;
    }

    public function isEnabled()
    {
        return (bool) Mage::getStoreConfig('blueacorn_cachemanagementmods/general/enabled');
    }

}