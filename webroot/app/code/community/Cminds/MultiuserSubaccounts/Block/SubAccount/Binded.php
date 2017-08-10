<?php

class Cminds_MultiuserSubaccounts_Block_SubAccount_Binded extends Cminds_MultiUserAccounts_Block_SubAccount_Abstract
{

    public function __construct()
    {
        parent::__construct();

        $items = $this->getBindedCustomers();
        $this->setItems($items);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('Customers Binded'));
        }
        $pager = $this->getLayout()->createBlock('page/html_pager', 'multiuseraccounts.customer.binded.pager')
            ->setCollection($this->getItems());
        $this->setChild('pager', $pager);
        return $this;
    }

    public function getBindedCustomers()
    {
        $subAccountData = Mage::helper('cminds_multiuseraccounts')->isSubAccountMode();
        $bindedCustomers = Mage::getModel('cminds_multiusersubaccounts/binded')->getCollection()->addFieldToFilter('subaccount_id', array('eq' => $subAccountData->getId()));
        return $bindedCustomers;
    }

    public function getCustomerLabel($id) {
        $customer = Mage::getModel('customer/customer')->load($id);
        $name = $customer->getFirstname() . ' ' . $customer->getLastname();
        return $name;
    }

    public function getPostActionUrl()
    {
        return $this->getUrl('customer/account/switchCustomer');
    }

    public function isParentInSession()
    {
        $session = Mage::getSingleton('customer/session');
        if($session->getParentCustomerId()) {
            return $session->getParentCustomerId();
        }
    }
}