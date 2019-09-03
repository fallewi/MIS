<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2016-08-09T12:40:22+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/MagestoreAffiliatePlus.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_MagestoreAffiliatePlus extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Magestore Affiliate Plus Data Export',
            'category' => 'Order',
            'description' => 'Export affiliate data stored by the Magestore Affiliate Plus extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'Magestore_Affiliateplus',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        // Fetch fields to export
        $order = $collectionItem->getOrder();

        if (!$this->fieldLoadingRequired('magestore_affiliateplus')) {
            return $returnArray;
        }

        try {
            $transactionCollection = Mage::getModel('affiliateplus/transaction')->getCollection();
            $transactionCollection->addFieldToFilter('order_id', $order->getId());

            if ($transactionCollection->count()) {
                $this->_writeArray = & $returnArray['magestore_affiliateplus_transaction'];
                $storeOrder = $transactionCollection->getFirstItem();
                foreach ($storeOrder->getData() as $key => $value) {
                    $this->writeValue($key, $value);
                }
            }
        } catch (Exception $e) {

        }

        // Done
        return $returnArray;
    }
}