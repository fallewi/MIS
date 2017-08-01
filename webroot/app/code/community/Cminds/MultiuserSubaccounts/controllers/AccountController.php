<?php
/**
 * @author CreativeMindsSolutions
 */

require_once 'Cminds/MultiUserAccounts/controllers/AccountController.php';

class Cminds_MultiuserSubaccounts_AccountController extends Cminds_MultiUserAccounts_AccountController
{
    public function bindedAction()
    {
        $customerId =  Mage::getSingleton('customer/session')->getCustomer()->getId();
        $helper = Mage::helper('cminds_multiuseraccounts');
        if(Mage::getSingleton('customer/session')->getSubAccount() || $helper->isSubAccount($customerId)){
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->_initLayoutMessages('catalog/session');

            $this->getLayout()->getBlock('head')->setTitle($this->__('Binded Customers'));
            $this->renderLayout();
        } else{
            $this->_forward('noRoute');
        }
    }

    public function switchCustomerAction()
    {
        $session = Mage::getSingleton('customer/session');
        $subAccountId = $session->getSubAccount()->getId();
        $customerId = $this->getRequest()->getParam('binded_customer_id');

        if(!Mage::helper('cminds_multiuseraccounts')->ifShareSession()) {
            $this->_getSession()->addError($this->__('This option is not working with split session'));
            $this->_redirect('*/*/binded');
            return $this;
        }
        if (empty($session->getParentCustomerId())) {
            $masterId = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($subAccountId)->getData('parent_customer_id');
            $session->setParentCustomerId($masterId);
        }

        if(!$this->isBound($subAccountId, $customerId)) {
            $this->_getSession()->addError('You are trying to switch session to Customer which are not bound to you.');
            $this->_redirect('*/*/binded');
            return $this;
        }

        $customer = Mage::getModel('customer/customer')->load($customerId);
        try {
            $session->setCustomer($customer);
            $this->_getSession()->addSuccess($this->__('You switched session to ' . $customer->getFirstname() . ' ' . $customer->getLastname()));
            $this->_redirect('*/*/binded');
            return $this;
        }
        catch (Exception $e) {
            $this->_getSession()->addError($this->__($e->getMessage()));
            $this->_redirect('*/*/binded');
            return $this;
        }

    }

    protected function isBound($subAccountId, $customerId)
    {
        $boundModel = Mage::getModel('cminds_multiusersubaccounts/binded');
        return $boundModel->isBound($subAccountId, $customerId);
    }

}