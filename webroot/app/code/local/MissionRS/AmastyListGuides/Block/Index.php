<?php
/**
 * @author MissionRS Victor Cortez
 * @package MissionRS_AmastyListGuides
 * Extending to add setOrder to title
 */ 
class MissionRS_AmastyListGuides_Block_Index extends Amasty_List_Block_Index
{ 
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        $lists = Mage::getResourceModel('amlist/list_collection')
            ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
            ->setOrder('title','asc')
            ->load();
            
        $this->setLists($lists);
        
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('My Favorites'));
        }
    }
}