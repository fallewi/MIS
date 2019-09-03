<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2018-10-12T15:21:01+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/QuickpayPayment.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_QuickpayPayment extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Quickpay_Payment Payment Data export',
            'category' => 'Order',
            'description' => 'Export payment data of Quickpay_Payment extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'Quickpay_Payment',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();

        if (!$this->fieldLoadingRequired('quickpay_payment')) {
            return $returnArray;
        }
        $order = $collectionItem->getOrder();
        $this->_writeArray = & $returnArray['quickpay_payment'];

        // Fetch fields to export
        $readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
        $table = Mage::getSingleton('core/resource')->getTableName('quickpaypayment_order_status');
        $binds = array(
            'order_number' => $order->getIncrementId(),
        );
        $dataRow = $readAdapter->fetchRow("SELECT * FROM {$table} WHERE ordernum = :order_number", $binds);

        if (is_array($dataRow)) {
            foreach ($dataRow as $key => $value) {
                $this->writeValue($key, $value);
            }
        }
        return $returnArray;
    }
}