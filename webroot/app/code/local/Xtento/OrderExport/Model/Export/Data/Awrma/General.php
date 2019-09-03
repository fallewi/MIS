<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2016-02-02T14:08:54+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Awrma/General.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Awrma_General extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'aheadWorks RMA information',
            'category' => 'Awrma',
            'description' => 'Export extended information about the aheadWorks RMA object.',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_AWRMA),
            'third_party' => true,
            'depends_module' => 'AW_Rma'
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray; // Write directly on object level
        // Fetch fields to export
        $object = $collectionItem->getObject();

        $rmaObject = Mage::getModel('awrma/entity')->load($object->getId());
        if ($this->fieldLoadingRequired('status_name')) {
            $this->writeValue('status_name', $rmaObject->getStatusName());
        }
        if ($this->fieldLoadingRequired('request_type_name')) {
            $this->writeValue('request_type_name', $rmaObject->getRequestTypeName());
        }
        if ($this->fieldLoadingRequired('is_active')) {
            $this->writeValue('is_active', $rmaObject->getIsActive());
        }
        if ($this->fieldLoadingRequired('url')) {
            $this->writeValue('url', $rmaObject->getUrl());
        }

        // Done
        return $returnArray;
    }
}