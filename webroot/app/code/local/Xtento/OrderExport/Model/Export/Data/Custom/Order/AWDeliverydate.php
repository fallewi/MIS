<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2016-07-04T13:16:26+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/AWDeliverydate.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_AWDeliverydate extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'aheadWorks Delivery Date Export',
            'category' => 'Order',
            'description' => 'Export delivery date of the aheadWorks Delivery Date extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'AW_Deliverydate',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();

        if (!$this->fieldLoadingRequired('aw_deliverydate')) {
            return $returnArray;
        }

        try {
            $this->_writeArray = & $returnArray['aw_deliverydate'];

            $deliverydate = Mage::getModel('deliverydate/delivery')->load($collectionItem->getOrder()->getId(), 'order_id');
            if ($deliverydate->getDeliveryDate() && !is_empty_date($deliverydate->getDeliveryDate())) {
                $this->writeValue('aw_deliverydate_date', $deliverydate->getDeliveryDate());
            }
            if ($deliverydate->getDeliveryNotice()) {
                $this->writeValue('aw_deliverydate_notice', $deliverydate->getDeliveryNotice());
            }
        } catch (Exception $e) {

        }

        // Done
        return $returnArray;
    }
}