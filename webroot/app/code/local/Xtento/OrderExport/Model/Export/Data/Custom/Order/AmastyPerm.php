<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2017-05-23T21:17:26+02:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Custom/Order/AmastyPerm.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Custom_Order_AmastyPerm extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    public function getConfiguration()
    {
        return array(
            'name' => 'Amasty Sales Reps and Dealers (Perm) Export',
            'category' => 'Order',
            'description' => 'Export sales rep (admin user) information stored by Amasty_Perm extension',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO),
            'third_party' => true,
            'depends_module' => 'Amasty_Perm',
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray['amasty_salesrep']; // Write on "amasty_salesrep" level
        // Fetch fields to export
        $order = $collectionItem->getOrder();

        if (!$this->fieldLoadingRequired('amasty_salesrep')) {
            return $returnArray;
        }

        try {
            $adminUserId = Mage::getModel('amperm/perm')->getUserByOrder($order->getId());
            if ($adminUserId > 0) {
                $adminUser = Mage::getModel('admin/user')->load($adminUserId);
                if ($adminUser->getId()) {
                    foreach ($adminUser->getData() as $key => $value) {
                        if ($key != 'firstname' && $key != 'lastname' && $key != 'email' && $key != 'username') {
                            continue;
                        }
                        $this->writeValue($key, $value);
                    }
                }
            }
        } catch (Exception $e) {

        }

        // Done
        return $returnArray;
    }
}