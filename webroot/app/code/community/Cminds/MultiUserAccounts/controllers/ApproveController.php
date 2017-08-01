<?php

class Cminds_MultiUserAccounts_ApproveController extends Mage_Core_Controller_Front_Action
{

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function getSender()
    {
        $storeId = $this->getStoreId();

        return array(
            'name' => Mage::getStoreConfig('trans_email/ident_support/name', $storeId),
            'email' => Mage::getStoreConfig('trans_email/ident_support/email', $storeId)
        );
    }

    public function getStoreId()
    {
        $store = Mage::app()->getStore();
        return $store->getId();
    }

    public function approveQuoteAction() {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $quoteId = $this->getRequest()->getParam('quote_id');
        $subAccountId = $this->getRequest()->getParam('subaccount_id');

        if(!$quoteId || !$subAccountId) {
            return $this->_redirect('*/*/subAccount');
        }

        $approver = Mage::getSingleton('customer/session')->getCustomer();
        $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($subAccountId);

        if($helper->isSubAccountMode()) {
            $approver = $helper->isSubAccountMode();
            if(!$approver->canApprove($subAccountId)) {

                $this->_getSession()->addError(
                    $helper->__('You are not allowed to Approve Cart of this User')
                );

                return $this->_redirect('customer/account/subAccount');
            }
        }

        try {
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            $quote->setData('quote_approve', Cminds_MultiUserAccounts_Model_SubAccount_Approvalstatuses::ASK_APPROVED);
            $quote->save();
            $this->sendApprovedMail($approver, $subAccount);
            $this->_redirect('customer/account/showCartItem/id', array('id' => $subAccountId));
        } catch(Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('customer/account/showCartItem/id', array('id' => $subAccountId));
        }
    }

    public function sendAskForApproveAction()
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $subAccount = $helper->isSubAccountMode();
        $store = Mage::app()->getStore();
        $storeId = $store->getId();
        $sender  = $this->getSender();

        if($helper->ifShareSession()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        } else {
            $customer = Mage::getModel('customer/customer')->load($subAccount->getParentCustomerId());
        }

        if(!$subAccount || ($customer->getId() == $subAccount->getCustomerId())) {
            Mage::getSingleton('customer/session')->setCustomer(Mage::getModel('customer/customer'))->setId(null);
            Mage::getSingleton('core/session')->addError(
                $helper->__('Please re log to send correctly ask for approval')
            );
            $this->_redirectUrl(Mage::getBaseUrl());
            return;
        }

        $quote = Mage::getModel('checkout/cart')->getQuote();

        try {
            $quote->setData('quote_approve', Cminds_MultiUserAccounts_Model_SubAccount_Approvalstatuses::ASK_SENT);
            $quote->save();
            $this->sendEmailToApprovers($subAccount, $customer, $sender, $quote, $storeId);
            $this->sendEmailToSender($subAccount, $customer, $sender, $storeId);

            Mage::getSingleton('core/session')->addSuccess(
                $helper->__('The order was sent for approval')
            );
            $this->_redirect('/');
        }
        catch(Exception $error) {
            Mage::getSingleton('core/session')->addError($error->getMessage());
            $this->_redirect('/');
        }
    }

    public function sendEmailToApprovers($subAccount, $customer, $sender, $quote)
    {
        $helper = Mage::helper('cminds_multiuseraccounts');

        if($helper->ifShareSession()) {
            $templateId = 'multiuseraccounts_email_approve_share';
        } else {
            $templateId = 'multiuseraccounts_email_approve';
        }

        $emailTemplate = Mage::getModel('core/email_template');
        $emailTemplate->setTemplateSubject(
            $helper->__(
                '%s has an order approval request for you',
                $subAccount->getName()
            )
        );
        $linkToCart = Mage::getUrl(
            'customer/account/showCartItem/',
            array('id' => $subAccount->getData('entity_id'))
        );

        if($subAccount->hasAssignedApprovers()) {
            $approvers = Mage::getModel('cminds_multiuseraccounts/subAccount')
                ->getCollection()
                ->getAssignedApprovers($subAccount);
        } else {
            $approvers = $subAccount->getApprovers();
        }

        $recipients = array();
        $vars = array();
        foreach($approvers as $approver) {
            $recipients[$approver->getEmail()] = $approver->getName();
        }
        $recipients[$customer->getEmail()] = $customer->getName();
        $vars['sub_first_name'] = $subAccount->getData('firstname');
        $vars['sub_last_name'] = $subAccount->getData('lastname');
        $vars['sub_email'] = $subAccount->getData('email');
        $vars['website_name'] = Mage::app()->getWebsite()->getName();
        $vars['quote'] = $quote;
        $vars['quote_id'] = $quote->getId();
        $vars['date'] = Mage::helper('core')->formatDate($quote->getCreatedAt(), 'long', true);
        $vars['link_to_cart'] = $linkToCart;

        $emailTemplate->sendTransactional(
            $templateId,
            $sender,
            array_keys($recipients),
            array_values($recipients),
            $vars,
            $this->getStoreId()
        );
    }

    public function sendEmailToSender($subAccount, $customer, $sender)
    {
        $helper = Mage::helper('cminds_multiuseraccounts');

        $emailToSub = Mage::getModel('core/email_template');
        $varsSub['email'] = $subAccount->getData('email');
        $varsSub['main_first_name'] = $customer->getData('firstname');
        $varsSub['main_last_name'] = $customer->getData('lastname');
        $varsSub['main_email'] = $customer->getData('email');

        $emailToSub->setTemplateSubject(
            $helper->__('Your cart approval request has been forwarded')
        );

        $emailToSub->sendTransactional(
            'multiuseraccounts_email_approve_sender',
            $sender,
            array($subAccount->getEmail()),
            array($subAccount->getName()),
            $varsSub,
            $this->getStoreId()
        );
    }

    public function sendApprovedMail($approver, $subAccount)
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $emailToSub = Mage::getModel('core/email_template');

        $emailVariables['main_email'] = $approver->getData('email');
        $emailVariables['main_first_name'] = $approver->getData('firstname');
        $emailVariables['main_last_name'] = $approver->getData('lastname');
        $sender = $this->getSender();

        $emailToSub->setTemplateSubject(
            $helper->__('Your cart was approved')
        );

        $emailToSub->sendTransactional(
            'multiuseraccounts_email_quote_approved',
            $sender,
            array($subAccount->getEmail()),
            array($subAccount->getName()),
            $emailVariables,
            $this->getStoreId()
        );
    }

    public function placeOrderAction()
    {
        if ($quoteId = $this->getRequest()->getParam('quote_id')) {
            $subAccountId = $this->getRequest()->getParam('subaccount_id');
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            try {
                $quote->collectTotals();
                $service = Mage::getModel('sales/service_quote', $quote);
                $service->submitAll();
                $order = $service->getOrder();
                $order->save();
                $quote->setData('quote_approve',
                    Cminds_MultiUserAccounts_Model_SubAccount_Approvalstatuses::ASK_APPROVED);
                $quote->setIsActive(0);
                $quote->save();

                Mage::getSingleton('core/session')->addSuccess($this->__('Order was placed.'));
                $this->_redirect('customer/account/showCartItem/id', array('id' => $subAccountId));
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                $this->_redirect('customer/account/showCartItem/id', array('id' => $subAccountId));
            }
        }
    }
}