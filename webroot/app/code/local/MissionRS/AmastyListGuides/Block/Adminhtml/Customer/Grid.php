<?php
 
class MissionRS_AmastyListGuides_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{ 
    protected function _afterLoadCollection()
    {        
        $collection = $this->getCollection();
        
        $entities = array();
        
        foreach($collection as $item) {
            $entities[] = $item->getId();
        }
        
        $lists = Mage::getModel('amlist/list')->getCollection()
                ->addFieldToFilter('customer_id', array('in' => $entities));
        
        $sharedLists = Mage::getModel('amlistl/shared')->getCollection()
                ->addFieldToFilter('customer_id', array('in' => $entities));
        
        $sharedLists->getSelect()->columns("GROUP_CONCAT(DISTINCT list_id) as list_ids");
        $sharedLists->getSelect()->group('customer_id');
        
        foreach($sharedLists as $sharedList) {          
            $list = $collection->getItemById($sharedList->getCustomerId());           
            if($list) {
                $list->setListIds($list->getListIds() . "," . $sharedList->getListIds());
            }            
        }
        
        foreach($lists as $origList) {          
            $list = $collection->getItemById($origList->getCustomerId());           
            if($list) {
                $list->setListIds($list->getListIds() . "," . $origList->getListId());
            }            
        }
        
        $allListIds = array();       
        foreach($collection as $item) {            
            $listIds = explode(',', $item->getListIds());
            $allListIds = array_merge($allListIds, $listIds);
        }
      
        $allListIds = array_unique($allListIds);
        $allListIds = array_filter($allListIds);
        
        $allLists = Mage::getModel('amlist/list')->getCollection()
                ->addFieldToFilter('list_id', array('in' => $allListIds));
        
        Mage::register('cls_all_lists', $allLists, true);
      
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '150',
            'index'     => 'email'
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header'    =>  Mage::helper('customer')->__('Group'),
            'width'     =>  '100',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));
        
        $this->addColumn('list_ids', array(
            'header'    =>  Mage::helper('customer')->__('Guides'),
            'width'     =>  '100',
            'index'     =>  'list_ids',    
            'renderer'  => 'amlistl/adminhtml_customer_renderer_list',
            'sortable'  => false,
            'filter_condition_callback' => array($this, 'listFilterCallback')
        ));  

        $this->addColumn('Telephone', array(
            'header'    => Mage::helper('customer')->__('Telephone'),
            'width'     => '100',
            'index'     => 'billing_telephone'
        ));

        $this->addColumn('billing_postcode', array(
            'header'    => Mage::helper('customer')->__('ZIP'),
            'width'     => '90',
            'index'     => 'billing_postcode',
        ));

        $this->addColumn('billing_country_id', array(
            'header'    => Mage::helper('customer')->__('Country'),
            'width'     => '100',
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ));

        $this->addColumn('billing_region', array(
            'header'    => Mage::helper('customer')->__('State/Province'),
            'width'     => '100',
            'index'     => 'billing_region',
        ));

        $this->addColumn('customer_since', array(
            'header'    => Mage::helper('customer')->__('Customer Since'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('customer')->__('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customer')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customer')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
    
    protected function listFilterCallback($collection, $column)
    {
        $val = $column->getFilter()->getValue();
        
        $val = explode(',', $val);
       
        $lists = Mage::getModel('amlist/list')->getCollection();
        
        foreach($val as $v) {            
             $lists->getSelect()->orWhere("title LIKE '%{$v}%'");
        }
       
        if (!empty($val)) {
            $customerIds = array();
            foreach ($lists as $list) {
                $customerIds[] = $list->getCustomerId();
            }

            $listIds = $lists->getAllIds();
            $sharedLists = Mage::getModel('amlistl/shared')
                    ->getCollection()
                    ->addFieldToFilter('list_id', array('in' => $listIds));
            foreach ($sharedLists as $list) {
                $customerIds[] = $list->getCustomerId();
            }
        }
 
        $collection->getSelect()->where('e.entity_id IN(?)', $customerIds);
    }

}
