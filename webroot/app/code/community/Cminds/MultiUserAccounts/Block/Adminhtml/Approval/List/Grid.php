<?php

class Cminds_MultiUserAccounts_Block_Adminhtml_Approval_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('entity_id');
        $this->setId('send_approval_grid');
        $this->setUseAjax(true);
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $subAccountCollection = Mage::getModel('cminds_multiuseraccounts/subAccount')->getCollection();
        $tableName = Mage::getSingleton('core/resource')->getTableName('cminds_multiuseraccounts/subAccount');
        $quoteTable = Mage::getSingleton('core/resource')->getTableName('sales/quote');
        foreach ($subAccountCollection as $customer) {
            $customerIds[] = $customer->getCustomerId();
        }

        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addFieldToFilter('entity_id', array('in' => $customerIds));

        $collection->getSelect()
            ->join(array('a' => $tableName), 'e.entity_id = a.customer_id', array(
                'a.parent_customer_id',
                'a.firstname as parent_firstname',
                'a.lastname as parent_lastname',
                'a.email as parent_email'
            ))
            ->join(array('b' => $quoteTable), 'e.entity_id = b.customer_id', array('quote_approve'))
            ->where("a.permission = '" . Cminds_MultiUserAccounts_Model_SubAccount_Permission::PERMISSION_NEED_APPROVAL . "'")
            ->where('b.is_active = "1" and b.quote_approve != "0"');
        $collection->addAttributeToSelect('a.firstname');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('customer')->__('ID'),
            'width' => '50px',
            'index' => 'entity_id',
            'type' => 'number',
        ));

        $this->addColumn('firstname', array(
            'header' => Mage::helper('customer')->__('Sub Account Firstname'),
            'index' => 'firstname'
        ));
        $this->addColumn('lastname', array(
            'header' => Mage::helper('customer')->__('Sub Account Lastname'),
            'index' => 'lastname'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('customer')->__('Sub Account Email'),
            'index' => 'email'
        ));

        $this->addColumn('parent_customer_id', array(
            'header' => Mage::helper('customer')->__('Parent Customer Id'),
            'index' => 'parent_customer_id',
            'filter_condition_callback' => array($this, 'filterCallback'),
        ));

        $this->addColumn('parent_firstname', array(
            'header' => Mage::helper('customer')->__('Parent Customer Firstname'),
            'index' => 'parent_firstname',
            'filter_condition_callback' => array($this, 'filterCallback'),
        ));

        $this->addColumn('parent_lastname', array(
            'header' => Mage::helper('customer')->__('Parent Customer Lastname'),
            'index' => 'parent_lastname',
            'filter_condition_callback' => array($this, 'filterCallback'),
        ));

        $this->addColumn('parent_email', array(
            'header' => Mage::helper('customer')->__('Parent Customer Email'),
            'index' => 'parent_email',
            'filter_condition_callback' => array($this, 'filterCallback'),
        ));

        $this->addColumn('quote_approve', array(
            'header' => Mage::helper('customer')->__('Cart status'),
            'index' => 'quote_approve',
            'type' => 'options',
            'options' => Mage::getSingleton('cminds_multiuseraccounts/subAccount_approvalstatuses')->getOptionArray(),
            'filter_condition_callback' => array($this, 'filterCallback'),
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function filterCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $indexColumn = $column->getIndex();

        switch ($indexColumn) {
            case 'parent_customer_id':
                $this->getCollection()->getSelect()->where("parent_customer_id like ?", "%$value%");
                break;
            case 'parent_firstname':
                $this->getCollection()->getSelect()->where("a.firstname like ?", "%$value%");
                break;
            case 'parent_lastname':
                $this->getCollection()->getSelect()->where("a.lastname like ?", "%$value%");
                break;
            case 'parent_email':
                $this->getCollection()->getSelect()->where("a.email like ?", "%$value%");
                break;
            case 'quote_approve':
                $this->getCollection()->getSelect()->where("b.quote_approve like ?", "%$value%");
                break;
        }
        return $this;
    }
}