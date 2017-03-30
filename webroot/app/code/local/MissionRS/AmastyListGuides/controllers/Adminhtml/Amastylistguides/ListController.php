<?php
 
class MissionRS_AmastyListGuides_Adminhtml_Amastylistguides_ListController extends Mage_Adminhtml_Controller_Action
{
    public function addToListAction()
    {
        $customers = $this->getRequest()->getParam('customer');
        
        $listID = $this->getRequest()->getParam('list');
       
        $sharedLists = Mage::getModel('amlistl/shared')
                ->getCollection()
                ->addFieldToFilter('list_id', $listID)
                ->addFieldToFilter('customer_id', array('in' => $customers));

        $sharedListsInfo = array();       
        foreach($sharedLists as $list) {            
            $sharedListsInfo[$list->getCustomerId() . "-" . $list->getListId()] = $list;            
        }
        
        if (is_array($customers)) {
            foreach ($customers as $customer) {                
                 if(array_key_exists($customer . "-" . $listID, $sharedListsInfo)) {
                     $sharedList = $sharedListsInfo[$customer . "-" . $listID];
                 }
                 else {
                     $sharedList = Mage::getModel('amlistl/shared');
                 }
                
                 $sharedList->setCustomerId($customer)
                            ->setListId($listID)
                            ->setCreatedAt(gmdate('Y-m-d H:i:s'))
                            ->save();                  
            }
        }
        
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Shared lists have been updated'));
        
        $this->_redirect('adminhtml/customer/index');
    }

    public function deleteFromListAction()
    {      
        $customers = $this->getRequest()->getParam('customer');

        $listID = $this->getRequest()->getParam('list');

        $sharedLists = Mage::getModel('amlistl/shared')
                ->getCollection()
                ->addFieldToFilter('list_id', $listID)
                ->addFieldToFilter('customer_id', array('in' => $customers));
         
        foreach ($sharedLists as $list) {
            $list->delete();
        }
        
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Shared lists have been updated'));
        
        $this->_redirect('adminhtml/customer/index');
    }

    protected function _isAllowed()
    {
        return true;
    }
}