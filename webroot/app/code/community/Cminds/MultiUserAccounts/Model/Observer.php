<?php

/**
 * @author CreativeMindsSolutions
 */
class Cminds_MultiUserAccounts_Model_Observer
{
    public function checkOrderCreationAuth($observer)
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $subAccount = $helper->isSubAccountMode();

        if (!$subAccount) {
            return;
        }

        if (!$subAccount->validateOrderLimits($observer->getQuote())) {
            throw Mage::exception('Mage_Core', $helper->__('You have reached the Order Amount Limit'));
        }

        if (!$helper->hasCreateOrderPermission()
            && !$helper->isQuoteApproved($observer->getQuote()->getId())
        ) {
            throw Mage::exception('Mage_Core', $helper->__('No Order creation permission for this account'));
        }

        $observer->getOrder()->setSubaccountId($subAccount->getId());

        return;
    }

    public function beforeCartView()
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $subAccount = $helper->isSubAccountMode();

        if ($subAccount) {
            if (!$helper->canAccessToCart()) {
                Mage::getSingleton('customer/session')->addError('Cart area is restricted only for master accounts');
                Mage::app()->getResponse()->setRedirect(Mage::getUrl("/"));
            }
        }
    }

    public function beforeCheckoutView()
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $subAccount = $helper->isSubAccountMode();

        if ($subAccount) {
            if (!$helper->haveAccessToCheckout()) {
                Mage::getSingleton('checkout/session')->addError('Checkout area is restricted only for master accounts');
                Mage::app()->getResponse()->setRedirect(Mage::getUrl("/"));
            }
        }
    }

    public function beforeSaveSubaccount($observer)
    {

        $subAccountData = $observer->getData('customer');
        $customer = Mage::getModel("customer/customer");

        $parentCustomerId = $subAccountData->getData('parent_customer_id');
        $parentCustomerData = Mage::getModel("customer/customer")->load($parentCustomerId);

        $parentWebsiteId = $parentCustomerData->getData('website_id');
        $parentGroupId = $parentCustomerData->getData('group_id');

        if (!$subAccountData->getCustomerId()) {

            $customer->setWebsiteId($parentWebsiteId)
                ->setStoreId($parentCustomerData->getData('store_id'))
                ->setFirstname($subAccountData->getFirstname())
                ->setLastname($subAccountData->getLastname())
                ->setEmail($subAccountData->getEmail())
                ->setGroupId($parentGroupId)
                ->setPassword($subAccountData->getPassword());
            try {
                $customer->save();

                $savedCustomer = Mage::getModel("customer/customer")->load($customer->getData('entity_id'));

                $savedCustomer->setData('confirmation', null)->save();

                $subAccountData->setData('customer_id', $customer->getData('entity_id'));
            } catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }
        } else {
            $subAccount = $customer->load($subAccountData->getCustomerId());

            if ($subAccount->getId()) {
                $subAccount
                    ->setFirstname($subAccountData->getFirstname())
                    ->setLastname($subAccountData->getLastname())
                    ->setEmail($subAccountData->getEmail())
                    ->setPasswordHash($subAccountData->getPasswordHash())
                    ->save();
            }
        }
    }


    public function onCustomerSaveAfter($observer)
    {

        $customerPostData = $observer->getData('customer');

        $subaccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($customerPostData->getId(),
            'customer_id');

        if ($subaccount->getId()) {
            try {
                $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                $table = Mage::getSingleton('core/resource')->getTableName('cminds_multiuseraccounts/subAccount');

                $write->query("UPDATE {$table} SET firstname = '" . $customerPostData->getFirstname() . "' WHERE customer_id = " . $customerPostData->getId() . ";");
                $write->query("UPDATE {$table} SET lastname = '" . $customerPostData->getLastname() . "' WHERE customer_id = " . $customerPostData->getId() . ";");
                $write->query("UPDATE {$table} SET email = '" . $customerPostData->getEmail() . "' WHERE customer_id = " . $customerPostData->getId() . ";");
                $write->query("UPDATE {$table} SET password_hash = '" . $customerPostData->getPasswordHash() . "' WHERE customer_id = " . $customerPostData->getId() . ";");
            } catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }
        }
    }

    public function afterOrderSave($observer)
    {

        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getId();
        $orderModel = Mage::getModel('sales/order')->load($orderId);
        $parentCustomerId = $orderModel->getParentCustomerId();

        /** Second save */
        if ($parentCustomerId != null && $parentCustomerId != $order->getCustomerId()) {

            $orderModel->setCustomerId($orderModel->getParentCustomerId());
            $orderModel->setCustomerFirstname($orderModel->getParentCustomerFirstname());
            $orderModel->setCustomerLastname($orderModel->getParentCustomerLastname());
            $orderModel->setCustomerEmail($orderModel->getParentCustomerEmail());

            $orderModel->save();
        }
        /** First save */
        if (!Mage::registry('first_save')) {
            Mage::register('first_save', true);

            $customerId = $order->getCustomerId();
            $mainAccountId = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($customerId,
                'customer_id')->getData('parent_customer_id');
            $mainAccount = Mage::getModel('customer/customer')->load($mainAccountId);

            $orderModel->setParentCustomerId($mainAccountId);
            $orderModel->setParentCustomerFirstname($mainAccount->getData('firstname'));
            $orderModel->setParentCustomerLastname($mainAccount->getData('lastname'));
            $orderModel->setParentCustomerEmail($mainAccount->getData('email'));

            $orderModel->save();
        }
    }

    public function onCustomerDeleteSubaccount($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $customerId = $customer->getId();

        $subAccounts = Mage::getModel('cminds_multiuseraccounts/subAccount')->getCollection()
            ->addFieldToFilter('parent_customer_id', array('eq' => $customerId));

        foreach ($subAccounts as $subAccount) {
            $subCustomer = Mage::getModel('customer/customer')->load($subAccount->getCustomerId());
            $subCustomer->delete();
        }

        if ($subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($customerId, 'customer_id')) {
            $subAccount->delete();
        }
    }

    public function beforeBlockToHtml(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        if ($helper->isEnabled()) {

            $grid = $observer->getBlock();

            /**
             * Mage_Adminhtml_Block_Customer_Grid
             */
            if ($grid instanceof Mage_Adminhtml_Block_Customer_Grid) {
                $grid->addColumnAfter(
                    'parent_customer',
                    array(
                        'header' => $helper->__('Parent Customer'),
                        'index' => 'parent_customer',
                        'renderer' => 'Cminds_MultiUserAccounts_Block_Adminhtml_Customer_Grid_Renderer_Parent',
                        'filter_condition_callback' => array($this, 'filterCallback'),
                    ),
                    'customer_since'
                );
            }
        }
    }

    public function beforeLayoutLoaded($observer) {
        $helper = Mage::helper('cminds_multiuseraccounts');

        if ($helper->isEnabled()) {

            $grid = $observer->getBlock();
            $request = Mage::app()->getRequest();

            /**
             * Mage_Adminhtml_Block_Customer_Grid
             */
            if (
                $grid instanceof Mage_Adminhtml_Block_Customer_Grid
                && $request->getActionName() != "index"
            ) {
                $grid->addColumnAfter(
                    'parent_customer',
                    array(
                        'header' => $helper->__('Parent Customer'),
                        'index' => 'parent_customer',
                        'renderer' => 'Cminds_MultiUserAccounts_Block_Adminhtml_Customer_Grid_Renderer_ParentCsv',
                        'filter_condition_callback' => array($this, 'filterCallback'),
                    ),
                    'customer_since'
                );
            }
        }
    }

    public function filterCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $subAccountCollection = Mage::getModel('cminds_multiuseraccounts/subAccount')->getCollection();

        $allParentCustomerIds = array();
        foreach ($subAccountCollection as $subAccount) {
            $allParentCustomerIds[] = $subAccount->getParentCustomerId();
        }
        $parentCustomerIDs = Mage::getModel('customer/customer')->getCollection()
            ->addFieldToFilter('entity_id', array('in' => $allParentCustomerIds))
            ->addFieldToFilter('firstname', array('like' => '%' . $value . '%'));

        $subAccountIds = array();

        foreach ($parentCustomerIDs as $customer) {
            $subAccountIds[] = $customer->getId();
        }
        $subAccountCollection->addFieldToFilter('parent_customer_id', array('in' => $subAccountIds));

        $result = array();
        foreach ($subAccountCollection->getData() as $customers) {

            $result[] = $customers['customer_id'];
        }

        $collection->addFieldToFilter('entity_id', array('in' => $result));

        return $this;
    }

    public function onSaveQuote(Varien_Event_Observer $observer)
    {
        if (Mage::helper('cminds_multiuseraccounts')->hasNeedApprovalPermission()) {
            $quote = $observer->getQuote();
            $quoteData = $quote->getData();

            $quoteOrig = Mage::getModel('sales/quote')->load($quote->getId());
            if ($origData = $quoteOrig->getOrigData()) {
                if (($quoteData['items_count'] != $origData['items_count'] || $quoteData['items_qty'] != $origData['items_qty'] || $quoteData['grand_total'] != $origData['grand_total']) && ($quote->getData('quote_approve') != Cminds_MultiUserAccounts_Model_SubAccount_Approvalstatuses::ASK_NOT_SENT)) {
                    Mage::getSingleton('customer/session')->setData('qty_changed', 1);
                }
            }
        }
    }

    public function disapproveMiniQuoteAfter(Varien_Event_Observer $observer)
    {
        if (Mage::helper('cminds_multiuseraccounts')->hasNeedApprovalPermission()) {
            $quote = $observer->getItem()->getQuote();
            if (Mage::getSingleton('customer/session')->getQtyChanged()) {
                $quote->setData('quote_approve',
                    Cminds_MultiUserAccounts_Model_SubAccount_Approvalstatuses::ASK_NOT_SENT);
                $quote->save();
                Mage::getSingleton('customer/session')->unsetData('qty_changed');
                Mage::getSingleton('customer/session')->addError('You edited your cart, to place order please send ask for approval again');
            }
        }
    }

    public function createCustomersToExist()
    {
        /**
         * $subAccount->save() launch observer subaccount_save_before to create new Customers binded to this sub-aacocunt
         */
        $subAccounts = Mage::getModel('cminds_multiuseraccounts/subAccount')->getCollection()
            ->addFieldToFilter('customer_id', array('null' => true));
        foreach ($subAccounts as $subAccount) {
            try {
                $subAccount->save();
            } catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }
        }
    }
}