<?php
/**
 * Copyright (c) 2013 S.L.I. Systems, Inc. (www.sli-systems.com) - All Rights Reserved
 * This file is part of Learning Search Connect.
 * Learning Search Connect is distribute under license,
 * go to www.sli-systems.com/LSC for full license details.
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 * 
 * System configuration backend model for the cron frequency system configuration
 *
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Model_System_Config_Backend_Cron extends Mage_Core_Model_Config_Data {
    
    const CRON_STRING_PATH = 'crontab/jobs/sli_search/schedule/cron_expr';
    const CRON_MODEL_PATH = 'crontab/jobs/sli_search/run/model';

    /**
     * When frequency system configuration saves, save the values from the frequency
     * and time as a cron string to a parsable path that the crontab will pick up
     */
    protected function _afterSave() {
        $isEnabled = Mage::helper('sli_search')->isFeedEnabled();

        $frequency = $this->getData('groups/cron/fields/frequency/value');
        $time = $this->getData('groups/cron/fields/time/value');

        if ($isEnabled) {
            $cronTab = Mage::helper('sli_search')->getCronTimeAsCrontab($frequency, $time);

            try {
                $this->saveCronTab($cronTab);
            } catch (Exception $e) {
                throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
            }
        }
    }

    /**
     * Saves the necessary core config data entries for the cron
     * to pull them from the database
     */
    public function saveCronTab($cronTab) {
        Mage::getModel('core/config_data')
            ->load(self::CRON_STRING_PATH, 'path')
            ->setValue($cronTab)
            ->setPath(self::CRON_STRING_PATH)
            ->save();
        Mage::getModel('core/config_data')
            ->load(self::CRON_MODEL_PATH, 'path')
            ->setValue((string) Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
            ->setPath(self::CRON_MODEL_PATH)
            ->save();
    }

}
