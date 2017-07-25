<?php

class Cminds_MultiUserAccounts_Block_Adminhtml_SubAccount_Convert
    extends Mage_Adminhtml_Block_Widget_Grid

{

    public function __construct()
    {
        parent::__construct();

        $this->setId('convert_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);;
    }


    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid
     */
    protected function _prepareCollection()
    {
        $currentCustomer = Mage::registry('current_customer');
        $collection = Mage::getResourceModel('cminds_multiuseraccounts/customer_collection');
        $collection
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname');

        $collection->addFieldToFilter(
            'entity_id',
            array('neq' => $currentCustomer->getId())
        );

        $collection->filterByGroupId(
            $currentCustomer->getGroupId()
        );

        $collection->filterByWebsiteId($currentCustomer->getWebsiteId());
        $collection->filterByMasterAccounts();
        $collection->filterByExistedSubAccounts();

        if (!Mage::registry('filter')) {
            $collection->addFieldToFilter('entity_id', array('eq' => 0));
        }

        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }


    protected function _prepareColumns()
    {

        $this->addColumn('id', array(
            'header' => Mage::helper('cminds_multiuseraccounts')->__('Id'),
            'type' => 'text',
            'width' => '50px',
            'name' => 'in_products',
            'align' => 'left',
            'index' => 'entity_id',
        ));

        $this->addColumn('first_name', array(
            'header' => Mage::helper('cminds_multiuseraccounts')->__('First Name'),
            'type' => 'text',
            'name' => 'in_products',
            'align' => 'left',
            'index' => 'firstname',
        ));

        $this->addColumn('last_name', array(
            'header' => Mage::helper('cminds_multiuseraccounts')->__('Last Name'),
            'type' => 'text',
            'name' => 'in_products',
            'align' => 'left',
            'index' => 'lastname',
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('cminds_multiuseraccounts')->__('Email'),
            'type' => 'text',
            'name' => 'in_products',
            'align' => 'left',
            'index' => 'email',
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt' => 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header' => Mage::helper('customer')->__('Group'),
            'width' => '100',
            'index' => 'group_id',
            'type' => 'options',
            'options' => $groups,
            'filter' => false,
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header' => Mage::helper('customer')->__('Website'),
                'align' => 'center',
                'width' => '80px',
                'type' => 'options',
                'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index' => 'website_id',
                'filter' => false,
            ));
        }

        $this->addColumn('Convert', array(
            'header' => Mage::helper('catalog')->__('Action'),
            'index' => 'convert_action',
            'renderer' => 'Cminds_MultiUserAccounts_Block_Adminhtml_SubAccount_Convert_Renderer_Convert',
            'filter' => false,
        ));


        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/subAccount/convertGrid', array('_current' => true));
    }

}
