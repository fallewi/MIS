<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2016-06-07T13:04:33+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/AWCustomerattributes.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_AWCustomerattributes extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'aheadWorks Customer Attributes Export',
            'category' => 'Order',
            'description' => 'Export customer attributes of the aheadWorks Customer Attributes extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'AW_Customerattributes',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();

        if (!$this->fieldLoadingRequired('aw_customerattributes') || !$collectionItem->getOrder()->getCustomerId()) {
            return $returnArray;
        }

        try {
            $this->_writeArray = & $returnArray['aw_customerattributes'];

            // Output attributes
            $attributeCollection = Mage::getModel('aw_customerattributes/value')->getCollection();
            $attributeCollection->addCustomerFilter($collectionItem->getOrder()->getCustomerId());
            foreach ($attributeCollection as $attribute) {
                $attributeModel = $attribute->getAttributeModel();
                $this->writeValue($attributeModel->getCode(), $attribute->getValue());
            }
        } catch (Exception $e) {

        }

        // Done
        return $returnArray;
    }
}