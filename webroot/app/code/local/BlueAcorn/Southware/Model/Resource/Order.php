<?php
/**
 * @package BlueAcorn_Southware
 * @version 1.0.0
 * @author BlueAcorn
 * @copyright Copyright (c) 2015 Blue Acorn, Inc.
 */

class BlueAcorn_Southware_Model_Resource_Order extends Mage_Sales_Model_Resource_Order
{
    const TABLE_NAME = 'amasty_amorderattr_order_attribute';

    /**
     * Resource Model function to connect to database and Set SouthWare Order ID
     *
     * @param $orderId
     * @param $southwareOrderId
     */
    public function setSouthWareOrderId($orderId, $southwareOrderId)
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');

        $query = "UPDATE " . self::TABLE_NAME . " SET southware_order_id = '{$southwareOrderId}' WHERE order_id = " . (int)$orderId;

        $writeConnection->query($query);
    }

    public function getCustomerComments($orderId)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $query = 'SELECT customer_note FROM ' . self::TABLE_NAME . ' WHERE order_id = ' . (int)$orderId;

        $customerComment = $readConnection->fetchOne($query);
        return $customerComment;
    }
}