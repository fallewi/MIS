<?php

/**
 * Sales order history block
 *
 * @author      CreativeMindsSolutions
 */
class Cminds_MultiUserAccounts_Block_Sales_Order_History extends Mage_Sales_Block_Order_History
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/history.phtml');
        $helper = Mage::helper('cminds_multiuseraccounts');

        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $subAccountId = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($customerId, 'customer_id');

        if (!$helper->ifShareSession() && $helper->isEnabled() && count($subAccountId->getData()) > 0) {
            $orders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $subAccountId->getData('parent_customer_id'))
                ->addFieldToFilter('state',
                    array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                ->setOrder('created_at', 'desc');
        } else {
            $orders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->addFieldToFilter('state',
                    array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                ->setOrder('created_at', 'desc');
        }

        if ($subAccount = $helper->isSubAccountMode()) {
            if (!$helper->canViewAllOrders()) {
                $orders->addFieldToFilter('subaccount_id', $subAccount->getId());
            }
        }

        $this->setOrders($orders);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('My Orders'));
    }
}
