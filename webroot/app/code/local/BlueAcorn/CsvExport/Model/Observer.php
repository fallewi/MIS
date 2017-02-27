<?php
/**
 * @package     BlueAcorn\CsvExport
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2017 Blue Acorn, Inc.
 */

class BlueAcorn_CsvExport_Model_Observer
{
    public function createMarketingCsv()
    {
        $helper = Mage::helper('blueacorn_csvexport');
        $session = Mage::getSingleton('core/session');
        $manualFlag = ($session->getManualRunFlag(true)) ? 1 : 0;
        $newFileName = Mage::getModel('blueacorn_csvexport/marketingfeed')->marketingCollection($helper, $manualFlag);
        if($newFileName && $helper->isEmailEnabled()){
            Mage::helper('blueacorn_csvexport')->sendMail($newFileName, $manualFlag);
        }
        elseif(!$helper->isEmailEnabled()){
            Mage::getSingleton('core/session')->addError('Email functionality is disabled');
        }
        return;
    }
}