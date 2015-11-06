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
 * Attributes MiniGrid source model
 * 
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Model_System_Config_Source_Minigrid_Attributes extends SLI_Search_Model_System_Config_Source_Minigrid_Abstract {

    /**
     * Minigrid field source. One column with a list of product attributes
     *
     * @return array
     */
    protected function _getFields() {
        return array(
            "attribute" => array(
                "width" => "100%",
                "type" => "select",
                "options" => Mage::getModel('sli_search/system_config_source_attributes')->toOptionArray()
            )
        );
    }

}