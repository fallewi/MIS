<?php
/**
 * @package     BlueAcorn\CsvExport
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2017 Blue Acorn, Inc.
 */

class BlueAcorn_CsvExport_Model_Observer
{
    public function createMarketingCsv($helper, $manualFlag = null)
    {
        $fileName = Mage::getModel('blueacorn_csvexport/marketingfeed')->marketingCollection($helper, $manualFlag);
        Mage::helper('blueacorn_csvexport')->sendMail($fileName, $manualFlag);
    }
}