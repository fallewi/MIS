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
 * Minigrid system config field type source model abstract
 * Provides base functionality for minigrid source models
 * as the toOptionArray is specialized to the minigrid type
 * 
 * @package SLI
 * @subpackage Search
 */

abstract class SLI_Search_Model_System_Config_Source_Minigrid_Abstract {
    
    /**
     * Default values of field array. Field array defines
     * the fields on the grid.
     * 
     * @return array
     */
    abstract protected function _getFields(); 
    
    /**
     * Add the additional grid type as a viable type on the form
     *
     * Note: Have to add value and label to each field array because
     * the frontend renderer requires value and label to be set
     * when under score scope.
     * 
     * @return array
     */
    public function toOptionArray() {
        $fields = $this->_getFields();
        foreach($fields as $key => $field) {
            $fields[$key]['value'] = 1;
            $fields[$key]['label'] = 1;
        }
        
        return $fields;
    }
    
}