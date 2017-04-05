<?php
 
class MissionRS_AmastyListGuides_Block_Index extends Amasty_List_Block_Index
{ 
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        
        $sharedListIds = Mage::helper('amlistl')->getSharedListIds($customerId);  
        
        $lists = Mage::getResourceModel('amlist/list_collection')             
            ->setOrder('title','asc');
      
        $lists->getSelect()->where("list_id IN(?) OR customer_id = {$customerId}", $sharedListIds);
        $lists->load(); 
            
        $this->setLists($lists);
        
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('My Favorites'));
        }
    }
}