<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2018-10-12T16:39:01+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/ShipperHq.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_ShipperHq extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'ShipperHQ data export',
            'category' => 'Order',
            'description' => 'Export additional information stored by the ShipperHQ extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'Shipperhq_Shipper',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();

        if (!$this->fieldLoadingRequired('shipperhq_packages')) {
            return $returnArray;
        }

        $order = $collectionItem->getOrder();
        $orderId = $order->getId();

        try {
            $this->_writeArray = &$returnArray['shipperhq_packages']; // Write on "shipperhq_packages" level
            $packageCollection = Mage::getModel('shipperhq_shipper/order_packages')->loadByOrderId($orderId);
            if ($packageCollection && $packageCollection->count()) {
                foreach ($packageCollection as $package) {
                    $this->_writeArray = &$returnArray['shipperhq_packages'][];
                    foreach ($package->getData() as $key => $value) {
                        $this->writeValue($key, $value);
                    }
                }
            }
        } catch (Exception $e) {

        }
        $this->_writeArray = & $returnArray;

        return $returnArray;
    }
}