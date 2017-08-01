<?php

class Cminds_MultiUserAccounts_TransferCartController
    extends Mage_Core_Controller_Front_Action
{

    public function sendCartAction()
    {
        $helper = Mage::helper('cminds_multiuseraccounts');

        if (!$this->getRequest()->isPost()) {
            $this->_forward('noRoute');
            return $this;
        }

        if (!$helper->isEnabled()
            || !$helper->isSubAccountMode()
            || !$helper->isTransferCartEnabled()
        ) {
            $this->_forward('noRoute');
            return $this;
        }

        $subAccount = $helper->isSubAccountMode();
        $cartReceivers = $this->getRequest()->getParam('cart_receivers');
        $cart = Mage::getModel('checkout/cart')->getQuote();

        if (!$cartReceivers) {
            Mage::getSingleton('core/session')->addError(
                $helper->__('Missing Cart Receivers')
            );
            $this->_redirect('checkout/cart/index');
            return $this;
        }

        if (!$this->validateReceivers($subAccount, $cartReceivers)) {
            Mage::getSingleton('core/session')->addError('Wrong Receivers');
            $this->_redirect('checkout/cart/index');
            return $this;
        }

        try {
            $quote = Mage::getModel('sales/quote')->load($cart->getId());
            foreach ($cartReceivers as $key => $receiverId) {
                $this->createCartTransfer($receiverId, $quote, $subAccount);
            }

            $quote->setIsActive(0)->save();

            Mage::getSingleton('core/session')->addSuccess(
                $helper->__('Your cart was succesfully transfered to chosen Sub Account/s')
            );
            $this->_redirect('home');
            return $this;
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('checkout/cart/index');
            return $this;
        }
    }

    public function validateReceivers($subAccount, $cartReceivers)
    {
        $restSubAccount = $subAccount->getOtherSubAccounts(true);

        if (count(array_intersect($cartReceivers, $restSubAccount)) == count($cartReceivers)) {
            return true;
        }

        return false;
    }

    public function createCartTransfer($receiverId, $quote, $subAccount)
    {
        $receiver = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($receiverId);
        $transfer = Mage::getModel('cminds_multiuseraccounts/transfer');
        $data['subaccount_id'] = $receiverId;
        $data['quote_id'] = $quote->getId();
        $data['was_transfered'] = 0;
        $data['creator_id'] = $subAccount->getId();

        $transfer->addData($data);
        $transfer->save();
        $this->sendEmailToReceiver($receiver, $quote, $transfer, $subAccount);
    }

    public function sendEmailToReceiver($receiver, $quote, $transfer, $subAccount)
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $store = Mage::app()->getStore();
        $storeId = $store->getId();
        $sender  = array(
            'name' => Mage::getStoreConfig('trans_email/ident_support/name', $storeId),
            'email' => Mage::getStoreConfig('trans_email/ident_support/email', $storeId)
        );

        $emailTemplate = Mage::getModel('core/email_template');
        $emailTemplate->setTemplateSubject(
            $helper->__(
                '%s has shared his Cart',
                $subAccount->getName()
            )
        );
        $linkToCart = Mage::getUrl(
            'customer/account/transferview',
            array('id' => $transfer->getId())
        );

        $vars['name'] = $subAccount->getName();
        $vars['email'] = $subAccount->getData('email');
        $vars['website_name'] = Mage::app()->getWebsite()->getName();
        $vars['quote'] = $quote;
        $vars['quote_id'] = $quote->getId();
        $vars['date'] = Mage::helper('core')->formatDate($quote->getUpdatedAt(), 'long', true);
        $vars['link_to_cart'] = $linkToCart;

        $emailTemplate->sendTransactional(
            'multiuseraccounts_email_transfer_cart',
            $sender,
            array($receiver->getEmail()),
            array($receiver->getName()),
            $vars,
            $storeId
        );
    }

    public function transferAction()
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $subAccount = $helper->isSubAccountMode();

        if (!$helper->isEnabled()
            || !$helper->isTransferCartEnabled()
            || !$subAccount
            || !$subAccount->hasCreateOrderPermission()
        ) {
            $this->_forward('noRoute');
            return $this;
        }

        $transferId = $this->getRequest()->getParam('id');
        $transfer = Mage::getModel('cminds_multiuseraccounts/transfer')->load($transferId);

        if (!$transferId
            || $transfer->getSubaccountId() != $subAccount->getId()
        ) {
            $this->_forward('noRoute');
            return $this;
        }

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $transferedQuote = Mage::getModel('sales/quote')->load($transfer->getQuoteId());
        $currentQuoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        $currentQuote = Mage::getModel('sales/quote')->load($currentQuoteId);
        $transaction = Mage::getModel('core/resource_transaction');

        try {
            $currentQuote->setIsActive(0);
            $transferedQuote->assignCustomer($customer);
            $transferedQuote->setIsActive(1);
            $transfer->setWasTransfered(1);

            $transaction
                ->addObject($transfer)
                ->addObject($currentQuote)
                ->addObject($transferedQuote);

            $transaction->save();

            Mage::getSingleton('core/session')->addSuccess(
                $helper->__('Cart was transfered succesfully')
            );
            $this->_redirect('checkout/cart/index');
            return $this;
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('customer/account/index');
            return $this;
        }
    }


}
