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
 * Cron activities for the sli feed generation
 *
 * @package SLI
 * @subpackage Search
 */
class SLI_Search_Model_Cron {

    /**
     * Generates the feeds and sends email of status when done
     */
    public function generateFeeds() {
        if(!Mage::getStoreConfig('sli_search/cron/disabled'))
        {
            try {
                /** @var $helper SLI_Search_Helper_Feed */
                $helper = Mage::helper('sli_search/feed');
                $msg = $helper->generateFeedsForAllStores();
            }
            catch (SLI_Search_Exception $e) {
                $msg = $e->getMessage();
            }
            catch (Exception $e) {
                $msg = "Unknown Error: {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}. Please contact your sli provider.";
            }

            $this->_sendEmail($msg);
        }
    }

    /**
     * If there is a system config email set, send out the cron notification
     * email.
     *
     * @param $msg String
     */
    protected function _sendEmail($msg) {
        $sliHelper = Mage::helper('sli_search');
        Mage::getModel('sli_search/email')
            ->setData('msg', $sliHelper->formatEmailOutput($msg['messages']))
            ->setData('subject', 'SLI Scheduled Feed Generation')
            ->setData('email', Mage::helper('sli_search')->getCronEmail())
            ->send();
    }
}