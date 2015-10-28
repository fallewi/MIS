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
 * Determines next time the cron will run on the feeds.
 * 
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Block_System_Config_Frontend_Feed_Next extends Mage_Adminhtml_Block_System_Config_Form_Field {

    /**
     * Returns the config xml set job code for the sli cron job
     *
     * @return string
     */
    protected function _getSliCronJobCode() {
        $jobCode = Mage::getConfig()->getNode('crontab/sli_search/job_code');

        if (!$jobCode) {
            Mage::throwException("No cron job code set for sli_search cron job in config xml.");
        }
        return $jobCode;
    }

    /**
     * Renders the next scheduled cron time first from the cron table and then
     * from the set cron time if Magento hasnt scheduled it.
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return <type>
     */
    protected function  _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $crons = Mage::getResourceModel('cron/schedule_collection')
            ->addFieldToFilter("job_code", $this->_getSliCronJobCode());
        $crons->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns(array("scheduled_at" => 'max(scheduled_at)'));

        $scheduledAt = $crons->getFirstItem()->getData('scheduled_at');

        if (!$scheduledAt) {
            $helper = Mage::helper('sli_search');
            $scheduledAt = $helper->getNextRunDateFromCronTime();
        }

        return $scheduledAt;
    }

}