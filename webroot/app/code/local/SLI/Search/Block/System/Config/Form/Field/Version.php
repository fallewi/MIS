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
 * Custom field to display the version number of the SLI module in the system configuration
 *
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Block_System_Config_Form_Field_Version extends Varien_Data_Form_Element_Abstract
{

    public function getElementHtml() {
        /* @var $modules Mage_Core_Model_Config_Element */

        $modules = Mage::getConfig()->getNode('modules')->children();
        $info = $modules->SLI_Search->asArray();

        return isset($info['version']) ? $info['version'] : '';
    }
}
