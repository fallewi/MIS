<?php

/**
 * @package     BlueAcorn\CsvExport
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2017 Blue Acorn, Inc.
 */
class BlueAcorn_CsvExport_Adminhtml_MarketingController extends Mage_Adminhtml_Controller_Action
{
    /**
     *Controller method used to kick off the update process
     */
    public function indexAction()
    {
        $helper = Mage::helper('blueacorn_csvexport');
        if ($helper->isEnabled()) {
            Mage::getModel('blueacorn_csvexport/observer')->createMarketingCsv(true);
            $this->_redirectReferer();
        } else {
            Mage::getSingleton('core/session')->addError("The Blue Acorn Automated Marketing CSV module is not enabled");
            $this->_redirectReferer();
        }
    }
}