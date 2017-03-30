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
}