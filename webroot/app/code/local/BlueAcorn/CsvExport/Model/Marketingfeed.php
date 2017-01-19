<?php
/**
 * @package     BlueAcorn\CsvExport
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2017 Blue Acorn, Inc.
 */

class BlueAcorn_CsvExport_Model_Marketingfeed extends Mage_Core_Model_Abstract
{

    public function marketingCollection($helper, $manualFlag)
    {
        $excludedGroups = $helper->getExcludedGroups();
        if($manualFlag){
            $fromDate = date('Y-m-d H:i:s', strtotime($helper->getFromDate() . '17:00:00'));
            $toDate   = date('Y-m-d H:i:s',strtotime($helper->getToDate() . '16:59:59'));
        }
        else{
            $fromDate = date('Y-m-d H:i:s', strtotime('-1 days 17:00:00' ));
            $toDate = date('Y-m-d H:i:s', strtotime('today 16:59:59'));
        }

        $collection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('status', array('nin' => array('canceled','closed')))
            ->addFieldToFilter('customer_group_id', array('nin' => $excludedGroups ))
            ->addFieldToFilter('customer_id', array('nin' => 18039));
//            ->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate))
//            ->addFieldToFilter('admin_id', array())

        $fileName = $this->saveToFile($collection, $manualFlag);
        return $fileName;
    }

    private function saveToFile($collection, $manualFlag)
    {
        $helper = Mage::helper('blueacorn_csvexport');
        $fileName = (string)$helper->getFileName();
        $directory = (string)$helper->getFileLocation();

        $io = new Varien_Io_File();
        $path = Mage::getBaseDir('var') . DS . $directory;
        if($manualFlag){
            $file = $path . DS . $fileName . date('Ymd', strtotime($helper->getFromDate())) . 'to'. date('Ymd',strtotime($helper->getToDate())) . '.csv';
        }
        else{
            $file = $path . DS . $fileName . date('Ymd') . '.csv';
        }
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);

        $headerData = array('Transaction Id', 'Revenue', 'Caller ID', 'Shipping Phone', 'Item Name', 'Item Quantity', 'Item price each', 'Item SKU');
        $io->streamWriteCsv($headerData);
        foreach ($collection as $order) {
            $data = array(
                $order->getId(),
                $order->getSubtotal(),
                $order->getBillingAddress()->getTelephone(),
                $order->getShippingAddress()->getTelephone(),
                $this->getItemsNames($order),
                $this->getItemsQtys($order),
                $this->getItemsPrice($order),
                $this->getItemsSkus($order)
            );
            $io->streamWriteCsv($data);
        }
        return $file;
    }

    private function getItemsNames($order)
    {
        $names = array();
        foreach($order->getAllVisibleItems() as $item){
            $names[] .= $item->getName() . '|';
        }
        return implode(' ', $names);
    }

    private function getItemsQtys($order)
    {
        $qtys = array();
        foreach($order->getAllVisibleItems() as $item) {
            $qtys[] .= (int)$item->getQtyOrdered() . '|';
        }
            return implode(' ', $qtys);
    }

    private function getItemsPrice($order)
    {
        $prices = array();
        foreach($order->getAllVisibleItems() as $item) {
            $prices[] .= number_format($item->getPrice(), '2', '.', ',') . '|';
        }
        return implode(' ', $prices);
    }

    private function getItemsSkus($order)
    {
        $sku = array();
        foreach($order->getAllVisibleItems() as $item) {
            $sku[] .= $item->getSku() . '|';
        }
        return implode(' ', $sku);
    }
}