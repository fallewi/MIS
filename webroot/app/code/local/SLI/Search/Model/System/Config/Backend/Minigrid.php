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
 * Minigrid backend model
 * Serializes and unserializes the grid data to
 * the config data
 * 
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Model_System_Config_Backend_Minigrid extends Mage_Core_Model_Config_Data {
    
    /**
     * In the event of a minigrid with file, get the local tmp location of the 
     * image file that was uploaded by the font minigrid
     * 
     * @return string|false
     */
    protected function _getTmpFileNames() {
        if (isset($_FILES['groups']['tmp_name']) && is_array($_FILES['groups']['tmp_name'])) {
            if (isset($_FILES['groups']['tmp_name']["{$this->getGroupId()}"])) {
                $field = $_FILES['groups']['tmp_name']["{$this->getGroupId()}"]['fields'][$this->getField()];
                if (isset($field['value'])) {
                    return $field['value'];
                }
            }
        }
        return false;
    }
    
    /**
     * In the event that a file was uploaded,
     * this array will contain the filenames as they appear
     * on the uploaded file.
     * 
     * @return array
     */
    protected function _getFileNames() {
        $groups = $this->getData('groups');
        $values = $groups["{$this->getGroupId()}"]['fields'][$this->getField()]['value'];
        
        return $values;
    }
    
    /**
     * Serialize
     */
    protected function _beforeSave() {
        parent::_beforeSave();
        $groups = $this->getData('groups');
        $values = $groups["{$this->getGroupId()}"]['fields'][$this->getField()]['value'];
        
        if (is_array($values)) {
            $this->setValue(serialize(array_values($values)));
        }
        else {
            $this->setValue(serialize($values));
        }
    }
    
    /**
     * Unserialize
     */
    protected function _afterLoad() {
        parent::_afterLoad();
        $this->setValue(unserialize($this->getValue()));
    }
        
}