<?php
 
class MissionRS_AmastyListGuides_Helper_Data extends Mage_Core_Helper_Abstract
{ 
    public function getSharedListIds($customerId)
    {
        $sharedLists = Mage::getModel('amlistl/shared')
                ->getCollection()               
                ->addFieldToFilter('customer_id', $customerId);

        $listIds = array();
        
        foreach($sharedLists as $list) {            
            $listIds[] = $list->getListId();
        }
       
        return $listIds;
    } 
    
    protected function _getCsvHeaders($lists)
    {       
        foreach($lists as $list) {
            $keys = array_keys($list->getData());
            array_push($keys, 'product_price');
            return $keys;
        }
    }
    
    public function exportGuides()
    {
        $customerId = $this->_getCustomer()->getId();
        
        $sharedListIds = $this->getSharedListIds($customerId);
         
        $lists = Mage::getModel('amlist/item')->getCollection();

        $lists->getSelect()
                ->reset()
                ->from(array('main_table' => $lists->getMainTable()), array('list_id', 'product_id', 'qty'))
                ->join(array('list' => $lists->getTable('amlist/list')), 'main_table.list_id = list.list_id', array('title', 'customer_id', 'is_default'))
                ->where("list.customer_id = ?", $customerId)
                ->order("list.list_id ASC");
        
        if(!empty($sharedListIds)) {
            $lists->getSelect()->orWhere("list.list_id IN(?)", $sharedListIds);
        }

        $io = new Varien_Io_File();
        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $file = $path . DS . $name . '.csv';
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);
       
        $io->streamWriteCsv($this->_getCsvHeaders($lists));

        foreach ($lists as $list) {                
            $product = Mage::getModel('catalog/product')->load($list->getProductId());            
            $list->setProductId($product->getSku());            
            $list->setProductPrice($product->getFinalPrice());
            $io->streamWriteCsv($list->getData());
        }

        return array(
            'type' => 'filename',
            'value' => $file,
            'rm' => true
        );
    }
    
    public function isImportExportAllowed()
    {
        $users = explode(',', Mage::getStoreConfig('amlist/general/import_export'));
        
        return in_array($this->_getCustomer()->getId(), $users);        
    }
    
    public function isSpecialUser()
    {
        $users = explode(',', Mage::getStoreConfig('amlist/general/special_user'));
        
        return in_array($this->_getCustomer()->getId(), $users);        
    }

    protected function _getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

}