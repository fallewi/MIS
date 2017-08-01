<?php

class Cminds_MultiuserSubaccounts_Block_Adminhtml_SubAccount_Edit_Tab_Binded extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    private $_selectedCustomers;
    private $_selectedAllCustomers;

    public function __construct()
    {
        parent::__construct();
        $this->setId('subaccount_binded_customers');
        $this->setDefaultSort('firstname');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $customers = Mage::getModel('cminds_multiuseraccounts/subAccount')->getCollection();
        $customerIds = array();
        $parentIds = array();
        foreach ($customers as $customer)
        {
            $customerIds[] = $customer->getCustomerId();
            $parentIds[] = $customer->getParentCustomerId();
        }

        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addFieldToFilter('entity_id', array('nin' => $customerIds))
            ->addFieldToFilter('entity_id', array('nin' => $parentIds));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'field_name' => 'customers_ids[]',
            'values'    => $this->_getSelectedCustomers(),
            'align'     => 'center',
            'index'     => 'entity_id',
            'filter_condition_callback' => array($this, '_filterHasUrlConditionCallback')
        ));
        $this->addColumn('all_customers', array(
            'type'      => 'checkbox',
            'field_name' => 'all_customers_ids[]',
            'values'    => $this->_getSelectedAllCustomers(),
            'align'     => 'center',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'index'     => 'entity_id'
        ));

        $this->addColumn('firstname', array(
            'header'    => Mage::helper('catalog')->__('Customer Firstname'),
            'index'     => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header'    => Mage::helper('catalog')->__('Customer Lastname'),
            'index'     => 'lastname',
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('catalog')->__('Customer Email'),
            'index'     => 'email',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/subAccount/bindedGrid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return false;
    }

    private function _getSelectedCustomers() {
        $subAccountId = Mage::app()->getRequest()->getParam('id');

        if(!$this->_selectedCustomers) {
            $customers = Mage::getModel('cminds_multiusersubaccounts/binded')->getCollection()->addFilter('subaccount_id', $subAccountId);
            $_selectedCustomers = array();

            foreach($customers AS $link) {
                $_selectedCustomers[] = $link->getCustomerId();
            }

            $allCustomers = $this->_getSelectedAllCustomers();

            foreach($allCustomers AS $customer_id) {
                if(in_array($customer_id, $_selectedCustomers)) {
                    $this->_selectedCustomers[] = $customer_id;
                }
            }
        }

        return $this->_selectedCustomers;
    }

    private function _getSelectedAllCustomers() {
        if(!$this->_selectedAllCustomers) {
            $customers = Mage::getModel('customer/customer')->getCollection();
            $this->_selectedAllCustomers = array();

            foreach($customers AS $link) {
                $this->_selectedAllCustomers[] = $link->getId();
            }
        }
        return $this->_selectedAllCustomers;
    }

    protected function _filterHasUrlConditionCallback($collection, $column)
    {
        if($column->getFilter()->getValue() == 0) {
            $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $this->_getSelectedCustomers()));
        }

        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        
        if($value) {
            $this->getCollection()->addFieldToFilter('entity_id', array('in' => $this->_getSelectedCustomers()));
        }

        return $this;
    }
}
