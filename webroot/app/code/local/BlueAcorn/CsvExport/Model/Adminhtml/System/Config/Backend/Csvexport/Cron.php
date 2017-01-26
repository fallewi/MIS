<?php
/**
 * @package     BlueAcorn\CsvExport
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2017 Blue Acorn, Inc.
 */
class BlueAcorn_CsvExport_Model_Adminhtml_System_Config_Backend_Csvexport_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH = 'crontab/jobs/blueacorn_csvexport_cron/schedule/cron_expr';

    /**Logic to set crontab for csvexport
     * @throws Exception
     */
    protected function _afterSave()
    {
        $cronExprString = $this->getData('groups/general/fields/cron_expression/value');

        try {
            Mage::getConfig()->saveConfig(self::CRON_STRING_PATH, $cronExprString);
            Mage::getConfig()->cleanCache();
        } catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
        }
    }
}
