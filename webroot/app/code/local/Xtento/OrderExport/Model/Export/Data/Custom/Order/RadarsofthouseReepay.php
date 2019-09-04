<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2019-05-08T11:21:21+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/RadarsofthouseReepay.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_RadarsofthouseReepay extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Reepay Payment Gateway Export',
            'category' => 'Payment',
            'description' => 'Export payment information of the Reepay payment gateway',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'Radarsofthouse_Reepay',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();

        if (!$this->fieldLoadingRequired('reepay')) {
            return $returnArray;
        }
        $payment = $collectionItem->getOrder()->getPayment();
        if ($payment->getMethod() == 'reepay' || $payment->getMethod() == 'reepay_mobilepay' || $payment->getMethod() == 'reepay_viabill') {
            $this->_writeArray = & $returnArray['payment']['reepay'];

            // Fetch fields to export
            $readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
            $dataRow = $readAdapter->fetchRow("SELECT * FROM ". Mage::getSingleton('core/resource')->getTableName('reepay_order_status') ." WHERE order_id = " . $readAdapter->quote($payment->getOrder()->getIncrementId()));

            if (is_array($dataRow)) {
                foreach ($dataRow as $key => $value) {
                    $this->writeValue($key, $value);
                }
            }
        }
        return $returnArray;
    }
}
