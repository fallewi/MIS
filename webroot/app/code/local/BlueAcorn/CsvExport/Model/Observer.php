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
        $test = $this->getSql();
        $test1 = $test;
    }


    private function getSql()
    {
        return Mage::getModel('sales/order')
            ->getCollection()
            ->addFieldToFilter('status', array('nin' => array('canceled','closed')));

    }
}