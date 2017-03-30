<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_List
 */ 
class MissionRS_AmastyListGuides_Model_Observer
{
    public function addNewActions($observer) 
    {
        if (!$this->_isCustomerGrid($observer->getPage())){
            return $this;
        }        
        
        $block = $observer->getBlock();        
 
        $block->addItem('amlist_guide_separator', array(
            'label'=> '---------------------',
            'url'  => '' 
        ));   
        
       $lists = Mage::getResourceModel('amlist/list_collection')->setOrder('title','asc');       
       $optionsArr = array();
       foreach($lists as $list) {
            array_unshift($optionsArr, array('label'=> $list->getTitle() . ' Created by customer #' . $list->getCustomerId() , 'value'=> $list->getId()));
       }      
        array_unshift($optionsArr, array('label'=> '', 'value'=> ''));
        $block->addItem('amlist_guide_addtolist', array(
             'label'        => Mage::helper('customer')->__('Add to list'),
             'url'          => Mage::app()->getStore()->getUrl('*/amastylistguides_list/addToList'),
             'additional'   => array(
                'add_to_list'    => array(
                     'name'     => 'list',
                     'type'     => 'select',
                     'class'    => 'required-entry',
                     'label'    => Mage::helper('customer')->__('Guide'),
                     'values'   => $optionsArr
                 )
            )
        ));
       
        $block->addItem('amlist_guide_deletefromlist', array(
             'label'        => Mage::helper('customer')->__('Delete from list'),
             'url'          => Mage::app()->getStore()->getUrl('*/amastylistguides_list/deleteFromList'),
             'additional'   => array(
                'delete_from_list'    => array(
                     'name'     => 'list',
                     'type'     => 'select',
                     'class'    => 'required-entry',
                     'label'    => Mage::helper('customer')->__('Guide'),
                     'values'   => $optionsArr
                 )
            )
        ));
      
        return $this;
    }
    
    public function modifyJs($observer) 
    {
        $page = $observer->getResult()->getPage();
        if (!$this->_isSalesGrid($page)){
            return $this;
        }
        
        $js = $observer->getResult()->getJs();
        $js = str_replace('varienGridMassaction', 'amoaction', $js); 
        $observer->getResult()->setJs($js);
        
        return $this;
    }  
    
    protected function _isCustomerGrid($page)
    {
	   return in_array($page, array('adminhtml_customer', 'customer'));
    } 

    protected function isExtensionActive($extensionName)
    {
        $val = Mage::getConfig()->getNode('modules/' . $extensionName . '/active');
	    return ((string)$val == 'true');
    } 
    
    public function modifyOrderGridAfterBlockGenerate($observer){
        $permissibleActions = array('index', 'grid', 'exportCsv', 'exportExcel');
        $exportActions = array('exportCsv', 'exportExcel');
        
        if ( false === strpos(Mage::app()->getRequest()->getControllerName(), 'sales_order') || 
             !in_array(Mage::app()->getRequest()->getActionName(), $permissibleActions) ){
             
            return;
        }
        
        $export = in_array(
                    Mage::app()->getRequest()->getActionName(), $exportActions);
        
        $block = $observer->getBlock();
        
		$blockClass = Mage::getConfig()->getBlockClassName('adminhtml/sales_order_grid');
        if ($blockClass == get_class($block)
			&& Mage::getStoreConfig("amoaction/ship/addcolumn")) {
            
            $hlr = Mage::helper("amoaction");

            $block->addColumnAfter('amoaction_shipping', array(
                'header' => $hlr->__('Shipping'),
                'index' => 'product_images',
                'renderer'  => 'amoaction/adminhtml_renderer_shipping'.($export ? "_export" : ""),
                'filter' => false,
                'sortable'  => false,
            ), "entity_id");

        }
    } 
    
}