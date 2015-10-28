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
 */

class SLI_Search_Model_System_Config_Backend_Enabledsetting {
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('sli_search')->__('Enabled')),
            array('value' => 2, 'label'=>Mage::helper('sli_search')->__('Disabled')),
            array('value' => 3, 'label'=>Mage::helper('sli_search')->__('Only Feed Enabled')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            1 => Mage::helper('sli_search')->__('Enabled'),
            2 => Mage::helper('sli_search')->__('Disabled'),
            3 => Mage::helper('sli_search')->__('Only Feed Enabled'),
        );
    }
}
