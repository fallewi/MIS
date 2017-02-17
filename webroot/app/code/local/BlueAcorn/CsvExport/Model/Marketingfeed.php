<?php
/**
 * @package     BlueAcorn\CsvExport
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2017 Blue Acorn, Inc.
 */

class BlueAcorn_CsvExport_Model_Marketingfeed extends Mage_Core_Model_Abstract
{
    /** Build collection of order to make marketing CSV
     * @param $helper
     * @param $manualFlag
     * @return bool|string
     */
    public function marketingCollection($helper, $manualFlag)
    {
        $fileName = false;
        $excludedGroups = $helper->getExcludedGroups();
        $fromDate = date('Y-m-d H:i:s', strtotime('today 05:00:00' ));
        $toDate = date('Y-m-d H:i:s', strtotime('+1 days 05:00:00'));
        if($manualFlag){
            $fromDate = date('Y-m-d H:i:s', strtotime($helper->getFromDate() . '05:00:00'));
            $toDate   = date('Y-m-d H:i:s',strtotime($helper->getToDate() . '+1 days 05:00:00'));
        }

        $collection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('status', array('nin' => array('canceled','closed')))
            ->addFieldToFilter('customer_group_id', array('nin' => $excludedGroups ))
            ->addFieldToFilter('customer_id', array('nin' => 18039))
            ->addFieldToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate))
            ->addFieldToFilter('admin_id', array('neq' => 'NULL'));

        if($collection->getSize() > 0){
            $fileName = $this->saveToFile($collection, $manualFlag);
        }
        else{
            Mage::getSingleton('core/session')->addError('There were no orders to report with current parameters set');
        }
        return $fileName;
    }

    /**Save file to configured location
     * @param $collection
     * @param $manualFlag
     * @return string
     */
    private function saveToFile($collection, $manualFlag)
    {
        $helper = Mage::helper('blueacorn_csvexport');
        $fileName = (string)$helper->getFileName();
        $directory = (string)$helper->getFileLocation();

        $io = new Varien_Io_File();
        $path = Mage::getBaseDir('var') . DS . $directory;
        if($manualFlag){
            $newFileName = $fileName . date('Ymd', strtotime($helper->getFromDate())) . 'to'. date('Ymd',strtotime($helper->getToDate())) . '.csv';
        }
        else{
            $newFileName = $fileName . date('Ymd') . '.csv';
        }
        $file = $path . DS . $newFileName;
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);

        $headerData = array('Transaction Id', 'Revenue', 'Caller ID', 'Shipping Phone', 'Item Name', 'Item Quantity', 'Item price each', 'Item SKU');
        $io->streamWriteCsv($headerData);
        foreach ($collection as $order) {
            $data = array(
                $order->getIncrementId(),
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
        return $newFileName;
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