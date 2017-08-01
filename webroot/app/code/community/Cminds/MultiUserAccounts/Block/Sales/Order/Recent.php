<?php

/**
 * Sales order history block
 *
 * @author      CreativeMindsSolutions
 */
class Cminds_MultiUserAccounts_Block_Sales_Order_Recent extends Mage_Sales_Block_Order_Recent
{
    public function __construct()
    {
        parent::__construct();
        $helper = Mage::helper('cminds_multiuseraccounts');
        $subAccount = $helper->isSubAccountMode();
        if (!$helper->ifShareSession() && $subAccount) {
            $customer = Mage::getModel('customer/customer')->load($subAccount->getParentCustomerId());
        } else {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        }

        //TODO: add full name logic
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
            ->addAttributeToFilter('customer_id', $customer->getId())
            ->addAttributeToFilter('state',
                array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->addAttributeToSort('created_at', 'desc')
            ->setPageSize('5');

        if ($subAccount) {
            if (!$helper->canViewAllOrders()) {
                $orders->addFieldToFilter('subaccount_id', $subAccount->getId());
            }
        }

        $orders->load();

        $this->setOrders($orders);
    }
}
