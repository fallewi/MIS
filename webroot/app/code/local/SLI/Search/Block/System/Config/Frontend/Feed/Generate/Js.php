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
 * Javascript block for adminhtml JS text_list block to add functionality to the
 * generate feed button on the system configuration for Feed Settings.
 * 
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Block_System_Config_Frontend_Feed_Generate_Js extends Mage_Adminhtml_Block_Template {

    /**
     * Sets javascript template to be included in the adminhtml js text_list block
     */
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('sli/search/sysconfig/generate/js.phtml');
    }

    /**
     * Returns the run all feeds async url
     *
     * @return string
     */
    public function getGenerateUrl() {
        $curStore = Mage::app()->getStore();
        Mage::app()->setCurrentStore(1); //default store number...always 1
        $myUrl = Mage::getUrl('sli_search/search/runFeedGeneration', array('_secure' => Mage::app()->getStore()->isCurrentlySecure()));
        Mage::app()->setCurrentStore($curStore);
        return $myUrl;
    }
}