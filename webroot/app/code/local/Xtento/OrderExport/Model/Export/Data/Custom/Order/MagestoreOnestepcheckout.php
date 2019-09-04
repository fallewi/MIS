<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2017-01-30T17:42:46+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/MagestoreOnestepcheckout.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_MagestoreOnestepcheckout extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Magestore OneStepCheckout Data Export',
            'category' => 'Order',
            'description' => 'Export delivery date/time stored by the Magestore One Step Checkout extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'Magestore_Onestepcheckout',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        // Fetch fields to export
        $order = $collectionItem->getOrder();

        if (!$this->fieldLoadingRequired('magestore_onestepcheckout')) {
            return $returnArray;
        }

        try {
            $delivery = Mage::getModel('onestepcheckout/delivery')->load($order->getId(), 'order_id');
            if ($delivery->getId()) {
                $this->_writeArray = & $returnArray['magestore_onestepcheckout'];
                foreach ($delivery->getData() as $key => $value) {
                    $this->writeValue($key, $value);
                }
            }
        } catch (Exception $e) {

        }

        // Done
        return $returnArray;
    }
}