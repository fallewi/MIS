<?php

class Cminds_MultiUserAccounts_MailController extends Mage_Core_Controller_Front_Action
{
    public function sendAction()
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        if ($postData = $this->getRequest()->isPost()) {
            if ($helper->ifShareSession()) {
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $subAccount = Mage::getSingleton('customer/session')->getSubAccount();
            } else {
                $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
                $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($customerId, 'customer_id');
                $customer = Mage::getModel('customer/customer')->load($subAccount->getParentCustomerId());
            }

            if (!$subAccount || ($customer->getId() == $subAccount->getCustomerId())) {
                Mage::getSingleton('customer/session')->setCustomer(Mage::getModel('customer/customer'))->setId(null);
                Mage::getSingleton('core/session')->addError('Please re log to send correctly ask for approval');
                $this->_redirectUrl(Mage::getBaseUrl());
                return;
            }
            $linkToCart = Mage::getUrl('customer/account/showCartItem/',
                array('id' => $subAccount->getData('entity_id')));
            $quote = Mage::getModel('checkout/cart')->getQuote();
            $emailToMain = Mage::getModel('core/email_template');
            if ($helper->ifShareSession()) {
                $emailToMain->loadDefault('multiuseraccounts_email_approve_share');
            } else {
                $emailToMain->loadDefault('multiuseraccounts_email_approve');
            }
            $emailToMain->setTemplateSubject($subAccount->getFirstname() . ' ' . $subAccount->getLastname() . ' has an order approval request for you');
            $emailToMainVariables['email'] = $customer->getData('email');
            $emailToMainVariables['first_name'] = $customer->getData('firstname');
            $emailToMainVariables['last_name'] = $customer->getData('lastname');
            $emailToMainVariables['sub_first_name'] = $subAccount->getData('firstname');
            $emailToMainVariables['sub_last_name'] = $subAccount->getData('lastname');
            $emailToMainVariables['sub_email'] = $subAccount->getData('email');
            $emailToMainVariables['website_name'] = Mage::app()->getWebsite()->getName();
            $emailToMainVariables['quote'] = $quote;
            $emailToMainVariables['quote_id'] = $quote->getId();
            $emailToMainVariables['date'] = Mage::helper('core')->formatDate($quote->getCreatedAt(), 'long', true);
            $emailToMainVariables['link_to_cart'] = $linkToCart;

            $emailToMain->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
            $emailToMain->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));

            $emailToSub = Mage::getModel('core/email_template');

            $emailToSub->loadDefault('multiuseraccounts_email_approve_sender');
            $emailToSub->setTemplateSubject('Your cart approval request has been forwarded');
            $emailToSubVariables['email'] = $subAccount->getData('email');
            $emailToSubVariables['main_first_name'] = $customer->getData('firstname');
            $emailToSubVariables['main_last_name'] = $customer->getData('lastname');
            $emailToSubVariables['main_email'] = $customer->getData('email');

            $emailToSub->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
            $emailToSub->setSenderEmail(Mage::getStoreConfig('trans_email/ident_general/email'));

            try {
                $emailToMain->send($customer->getData('email'),
                    $customer->getData('firstname') . ' ' . $customer->getData('lastname'), $emailToMainVariables);
                $emailToSub->send($subAccount->getData('email'),
                    $subAccount->getData('firstname') . ' ' . $subAccount->getData('lastname'), $emailToSubVariables);
                $quote->setData('quote_approve', Cminds_MultiUserAccounts_Model_SubAccount_Approvalstatuses::ASK_SENT);
                $quote->save();
                Mage::getSingleton('core/session')->addSuccess('The order was sent for approval');
                $this->_redirect('/');
            } catch (Exception $error) {
                Mage::getSingleton('core/session')->addError($error->getMessage());
                $this->_redirect('/');
            }
        }
    }
}
