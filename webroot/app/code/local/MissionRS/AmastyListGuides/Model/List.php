<?php

class MissionRS_AmastyListGuides_Model_List extends Amasty_List_Model_List
{
    protected $_lists = array();
     
    public function createListFromCsv(array $data, $customerId)
    {
        $headers = array_shift($data);
        
        $specialUser = Mage::helper('amlistl')->isSpecialUser();
      
        foreach($data as $item) {
             
            foreach($item as $k => $i) {                
                $item[$headers[$k]] = $i;                
            }
            
            /* Do not allow to import order guides for different users for non-special customers */
            if (!$specialUser && !empty($item['customer_id'])) {
                if ($customerId != $item['customer_id']) {
                    throw new Exception('You are not allowed to export order guides for other customers');
                }
            }
            
            // skip empty rows
            if(empty($item['list_id']) && empty($item['title'])) {
                continue;
            }
            
           
            $delItem = preg_match("#_DEL_$#is", $item['product_id']);     
            if($delItem) {
                $item['product_id'] = preg_replace("#_DEL_$#is", "", $item['product_id']);
            }  
            $delList = preg_match("#_DEL_$#is", $item['list_id']);
            if($delList) {
                 $item['list_id'] = preg_replace("#_DEL_$#is", "", $item['list_id']);
            }
            
            $delCustomer = preg_match("#_DEL_$#is", $item['customer_id']);             
            if($delCustomer) {         
                $lists = Mage::getModel('amlist/list')->getCollection()
                        ->addFieldToFilter('customer_id', str_replace('_DEL_', '', $item['customer_id'])); 
                foreach($lists as $list) {
                    $list->delete();
                }
                continue;
            }  
            $customerId = !empty($item['customer_id']) ? $item['customer_id'] : $customerId; 
            
            $cacheKey = "{$item['list_id']}-{$customerId}-{$item['title']}";             
            if(!isset($this->_lists[$cacheKey])) {                 
                $this->_lists[$cacheKey] = Mage::getModel('amlist/list')->load($item['list_id']);       
                
                if(empty($item['list_id']) || ($this->_lists[$cacheKey]->getCustomerId() != $customerId)) { 
                    // get list by title & customer id
                    $this->_lists[$cacheKey] = Mage::getModel('amlist/list')->getCollection()
                            ->addFieldToFilter('customer_id', $customerId)
                            ->addFieldToFilter('title', $item['title'])
                            ->getFirstItem();
                }
                
                $list = $this->_lists[$cacheKey];
                if ($list->getCustomerId() != $customerId) {
                    $list = Mage::getModel('amlist/list');
                    $list->setCustomerId($customerId);
                    $list->setCreatedAt(gmdate('Y-m-d'));
                    $this->_lists[$cacheKey] = $list;
                }
            }
             
            $list = $this->_lists[$cacheKey];
            
            if($delList) {
                // delete list 
                 $list->delete();
                 continue;
            }           
            
            if(!empty($item['title'])) {
                $list->setTitle($item['title']);
            }
            if(!empty($item['is_default'])) {
                $list->setIsDefault($item['is_default']);
            }
           
            $list->save();
            
            if(empty($item['product_id'])) {
                continue;
            }
            
            if($delItem) {
                foreach($list->getItems() as $it) {  
                    if($it->getProductId() == $item['product_id'] || $it->getProduct()->getSku() == $item['product_id']) {
                        $it->delete();
                        continue 2;
                    }
                }
            }
            
            if (!(int) $item['product_id']) {
                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $item['product_id']);
            } else {
                $product = Mage::getModel('catalog/product')->load($item['product_id']);
                if (!$product->getId()) {
                    $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $item['product_id']);
                }
            }
 
            if (!$product->getId()) {
                continue;
            }
            $request = new Varien_Object();
            $request->setProduct($product->getId());
            $request->setQty($item['qty']);             
            $customOptions = $product->getTypeInstance()->prepareForCart($request, $product);
            $list->addItem($product->getId(), $customOptions);
            
        }                 
    }
}
