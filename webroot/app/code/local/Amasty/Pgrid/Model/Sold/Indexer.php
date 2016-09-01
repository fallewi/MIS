<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */
class Amasty_Pgrid_Model_Sold_Indexer extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('ampgrid')->__('Qty Sold');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('ampgrid')->__('Qty Sold');
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {

    }

    protected function _registerCatalogInventoryStockItemEvent(Mage_Index_Model_Event $event)
    {

    }

    public function matchEvent(Mage_Index_Model_Event $event)
    {
        return false;
    }

    public function reindexQtySold($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        $productIds = array_map('intval',$productIds);
        $this->_deleteQtyIndex($productIds);
        $this->_insertQtyIndex($productIds);
        return $this;
    }

    public function revertProductsSale($orderData)
    {
        foreach ($orderData as $productId => $data) {
            $orderItemId = $data['order_item_id'];
            $itemId = $this->_getItemId($orderItemId);
            if ($itemId) {
                $qty = $data['qty'];
                $this->_updateItemId($productId, $qty);
            }
        }

        return $this;
    }

    protected function _getItemId($orderItemId)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = sprintf("SELECT item_id FROM %s WHERE %s AND %s",
            Mage::getConfig()->getTablePrefix().$connection->getTableName('sales_flat_order_item'),
            $connection->quoteInto("item_id = ?", $orderItemId),
            $this->_getDateSold()
        );
        return $connection->fetchOne($query);
    }

    protected function _updateItemId($productId, $qty)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = sprintf("UPDATE %s set qty_sold = qty_sold - %d  WHERE %s",
            Mage::getConfig()->getTablePrefix().$connection->getTableName('am_pgrid_qty_sold'),
            $qty,
            $connection->quoteInto("product_id = ?", $productId)
        );
        return $connection->query($query);
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {

    }

    public function reindexAll()
    {
        $productIds = Mage::getModel('catalog/product')->getCollection()->getAllIds();
        if (!empty($productIds)) {
            $this->_deleteQtyIndex();
            $this->_insertQtyIndex($productIds);
        }
    }

    protected function _deleteQtyIndex($productIds = array())
    {
        $connection = Mage::getSingleton('core/resource')->getConnection(
            'core_write'
        );
        $binds = array();
        $query = sprintf('DELETE FROM %s', Mage::getConfig()->getTablePrefix().$connection->getTableName('am_pgrid_qty_sold'));

        if (!empty($productIds)) {
            $query .=  sprintf(' WHERE %s', $connection->quoteInto("product_id IN (?) ", $productIds));
        }
        $connection->query($query, $binds);
    }

    protected function _insertQtyIndex($productIds)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection(
            'core_write'
        );

        $query = sprintf(
            'INSERT INTO %s (product_id, qty_sold)
          SELECT o.product_id, sum(o.qty_ordered)-sum(o.qty_refunded) as qty_sold FROM %s AS o WHERE %s AND %s GROUP BY o.product_id',
            Mage::getConfig()->getTablePrefix().$connection->getTableName('am_pgrid_qty_sold'),
            Mage::getConfig()->getTablePrefix().$connection->getTableName('sales_flat_order_item'),
            $connection->quoteInto("o.product_id IN (?)", $productIds),
            $this->_getDateSold()

        );

        $connection->query($query);
    }

    protected function _getDateSold ()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection(
            'core_write'
        );
        $dateFrom = Mage::getStoreConfig('ampgrid/additional/qty_sold_from');
        $dateTo = Mage::getStoreConfig('ampgrid/additional/qty_sold_to');
        $addToSelect = ' 1 ';
        if ($dateFrom && $dateTo) {
            $addToSelect = sprintf(" created_at BETWEEN %s AND %s",
                $connection->quote($dateFrom),
                $connection->quote($dateTo));
        } elseif ($dateFrom && !$dateTo) {
            $addToSelect = sprintf(" created_at >= %s",
                $connection->quote($dateFrom));
        } elseif (!$dateFrom && $dateTo) {
            $addToSelect = sprintf(" created_at <= %s",
                $connection->quote($dateTo));
        }
        return $addToSelect;
    }
}