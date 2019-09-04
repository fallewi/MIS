<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2018-11-29T15:26:26+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/AWAdvancednewsletter.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_AWAdvancednewsletter extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'aheadWorks Advanced Newsletter Export',
            'category' => 'Order',
            'description' => 'Export data of the aheadWorks Advanced Newsletter extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO, Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER),
            'third_party' => true,
            'depends_module' => 'AW_Advancednewsletter',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();

        if ($entityType == Xtento_OrderExport_Model_Export::ENTITY_CUSTOMER) {
            $customerId = $collectionItem->getObject()->getId();
        } else {
            $customerId = $collectionItem->getOrder()->getCustomerId();
        }

        if (!$this->fieldLoadingRequired('aw_advancednewsletter') || empty($customerId)) {
            return $returnArray;
        }

        try {
            $this->_writeArray = & $returnArray['aw_advancednewsletter'];

            // Fetch fields to export
            $readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
            $dataRow = $readAdapter->fetchRow("SELECT * FROM " . Mage::getSingleton('core/resource')->getTableName('advancednewsletter/subscriber') . " WHERE customer_id = " . $readAdapter->quote($customerId));

            if (is_array($dataRow)) {
                foreach ($dataRow as $key => $value) {
                    $this->writeValue($key, $value);
                }
            }
        } catch (Exception $e) {

        }

        // Done
        return $returnArray;
    }
}