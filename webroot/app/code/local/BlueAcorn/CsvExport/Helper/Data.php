<?php


/**
 * @package     BlueAcorn\CsvExport
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2017 Blue Acorn, Inc.
 */ 
class BlueAcorn_CsvExport_Helper_Data extends Mage_Core_Helper_Abstract
{
    const IS_ENABLED    = 'blueacorn_csvexport/general/active';
    const FILE_NAME     = 'blueacorn_csvexport/general/file_name';
    const FILE_LOCATION = 'blueacorn_csvexport/general/file_location';

    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::IS_ENABLED);
    }

    public function getFileName()
    {
        return Mage::getStoreConfig(self::FILE_NAME);
    }

    public function getFileLocation()
    {
        return Mage::getStoreConfig(self::FILE_LOCATION);
    }
}