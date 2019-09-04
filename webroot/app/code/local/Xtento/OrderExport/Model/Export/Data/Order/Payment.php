<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2016-02-02T14:19:04+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/Export/Data/Order/Payment.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_Export_Data_Order_Payment extends Xtento_OrderExport_Model_Export_Data_Abstract
{
    private $_origWriteArray;

    public function getConfiguration()
    {
        return array(
            'name' => 'Payment information',
            'category' => 'Order Payment',
            'description' => 'Export payment information from the sales_flat_order_payment table.',
            'enabled' => true,
            'apply_to' => array(Xtento_OrderExport_Model_Export::ENTITY_ORDER, Xtento_OrderExport_Model_Export::ENTITY_INVOICE, Xtento_OrderExport_Model_Export::ENTITY_SHIPMENT, Xtento_OrderExport_Model_Export::ENTITY_CREDITMEMO, Xtento_OrderExport_Model_Export::ENTITY_QUOTE),
        );
    }

    public function getExportData($entityType, $collectionItem)
    {
        // Set return array
        $returnArray = array();
        $this->_writeArray = & $returnArray['payment']; // Write on payment level
        $this->_origWriteArray = & $this->_writeArray;
        // Fetch fields to export
        $order = $collectionItem->getOrder();
        $payment = $order->getPayment();

        if (!$this->fieldLoadingRequired('payment')) {
            return $returnArray;
        }

        // General Payment Data
        if ($payment) {
            foreach ($payment->getData() as $key => $value) {
                if ($key == 'additional_information') continue;
                $this->writeValue($key, $value);
            }

            try {
                if ($this->fieldLoadingRequired('method_title')) {
                    if ($payment->getMethodInstance()) {
                        $this->writeValue('method_title', $payment->getMethodInstance()->getTitle());
                    }
                }
            } catch (Exception $e) {
                // Could not get payment method instance - probably payment module was removed.
            }

            // Additional data - serialized array
            $additionalData = $payment->getAdditionalData();
            if (!empty($additionalData) && $this->fieldLoadingRequired('additional_fields')) {
                $additionalData = @unserialize($additionalData);
                if ($additionalData && is_array($additionalData)) {
                    $this->_writeArray = & $returnArray['payment']['additional_fields'];
                    foreach ($additionalData as $key => $value) {
                        if (!is_array($value)) {
                            $this->writeValue($key, $value);
                        }
                    }
                    if (isset($additionalData['transactions']) && is_array($additionalData['transactions'])) {
                        $this->_writeArray = & $returnArray['payment']['additional_fields']['transaction'];
                        foreach ($additionalData['transactions'] as $transaction) {
                            // M2e fields
                            foreach ($transaction as $tKey => $tValue) {
                                $this->writeValue($tKey, $tValue);
                            }
                        }
                    }
                }
            }

            // Additional information - serialized array
            $additionalInformation = $payment->getAdditionalInformation();
            if (is_array($additionalInformation) && $this->fieldLoadingRequired('additional_fields')) {
                $this->_writeArray = & $returnArray['payment']['additional_fields'];
                foreach ($additionalInformation as $key => $value) {
                    $this->writeValue($key, $value);
                }
                if (isset($additionalInformation['transactions']) && is_array($additionalInformation['transactions'])) {
                    $this->_writeArray = & $returnArray['payment']['additional_fields']['transaction'];
                    foreach ($additionalInformation['transactions'] as $transaction) {
                        // M2e fields
                        foreach ($transaction as $tKey => $tValue) {
                            $this->writeValue($tKey, $tValue);
                        }
                    }
                }
            }

            // Authorize.net authorize_cards
            if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.5.0.0', '>=') && $this->fieldLoadingRequired('authorize_cards')) {
                $additionalData = $payment->getAdditionalData();
                $additionalData = @unserialize($additionalData);
                if ($additionalData && is_array($additionalData)) {
                    if (isset($additionalData['authorize_cards'])) {
                        $this->_writeArray = & $returnArray['payment']['authorize_cards'];
                        foreach ($additionalData['authorize_cards'] as $cardInfo) {
                            if (!is_array($cardInfo)) continue;
                            foreach ($cardInfo as $key => $value) {
                                $this->writeValue($key, $value);
                            }
                            break;
                        }
                    }
                }
                $additionalData = $payment->getAdditionalInformation('authorize_cards');
                if ($additionalData && is_array($additionalData)) {
                    $this->_writeArray = & $returnArray['payment']['authorize_cards'];
                    foreach ($additionalData as $cardInfo) {
                        if (!is_array($cardInfo)) continue;
                        foreach ($cardInfo as $key => $value) {
                            $this->writeValue($key, $value);
                        }
                        break;
                    }
                }
            }
            $this->_writeArray = & $this->_origWriteArray;

            if ($this->fieldLoadingRequired('transactions/')) {
                $this->_writeArray = & $returnArray['payment']['transactions']; // Write on "transactions" level
                // Output payment transactions
                $txnCollection = Mage::getModel('sales/order_payment_transaction')->getCollection()
                    ->setOrderFilter($order)
                    ->addPaymentIdFilter($payment->getId());
                foreach ($txnCollection as $txn) {
                    $this->_writeArray = & $returnArray['payment']['transactions'][];
                    $txn->setOrderPaymentObject($payment);
                    foreach ($txn->getData() as $key => $value) {
                        $this->writeValue($key, $value);
                    }
                    $additionalInformation = $txn->getAdditionalInformation();
                    if (is_array($additionalInformation)) {
                        foreach ($additionalInformation as $key => $value) {
                            $this->writeValue('additional_' . $key, $value);
                        }
                    }
                }
            }
            $this->_writeArray = & $this->_origWriteArray;
        }
        $this->_writeArray = & $returnArray;
        // Done
        return $returnArray;
    }
}