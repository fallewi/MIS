<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2016-02-12T17:16:23+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/MagalterShippingoptions.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_MagalterShippingoptions extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Magalter Shipping Options (Checkout data) export',
            'category' => 'Order',
            'description' => 'Export custom fields stored by the Magalter_Shippingoptions extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'Magalter_Customshipping',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        // Fetch fields to export
        $order = $collectionItem->getOrder();

        if (!$this->fieldLoadingRequired('magalter_custom')) {
            return $returnArray;
        }

        try {
            $this->_writeArray = & $returnArray['magalter_custom']; // Write on "magalter_custom" level
            $customFields = Mage::getModel('magalter_customshipping/order_option')->getCollection()->addFieldToFilter(
                'order_id',
                $order->getId()
            );
            foreach ($customFields as $customField) {
                $this->_writeArray = &$returnArray['magalter_custom'][];
                foreach ($customField->getData() as $key => $value) {
                    $this->writeValue($key, $value);
                }
            }
        } catch (Exception $e) {

        }
        $this->_writeArray = & $returnArray;

        // Done
        return $returnArray;
    }
}