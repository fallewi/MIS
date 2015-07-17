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
 * Bottom javascript block for before body end. Includes javascript from
 * system configuration
 * 
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Block_Search_JS_Bottom extends Mage_Core_Block_Text {

    /**
     * Set text to be javascript from system configuration
     */
    protected function _construct() {
        parent::_construct();
        $helper = Mage::helper('sli_search');
        if ($helper->isFormEnabled(Mage::app()->getStore()->getId())) {
            $this->addText($helper->getFooterJs());
        }
    }

}