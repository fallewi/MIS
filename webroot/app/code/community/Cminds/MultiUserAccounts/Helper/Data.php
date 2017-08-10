<?php

/**
 * @author CreativeMindsSolutions
 */
class Cminds_MultiUserAccounts_Helper_Data extends Mage_Customer_Helper_Data
{

    const ENABLED_KEY = 'subAccount/general/enable';

    public function getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    public function isEnabled()
    {
        $cmindsCore = Mage::getModel("cminds/core");

        if ($cmindsCore) {
            $cmindsCore->validateModule('Cminds_Marketplace');
        } else {
            throw new Mage_Exception('Cminds Core Module is disabled or removed');
        }

        $storeId = $this->getStoreId() ? $this->getStoreId() : null;
        return (bool)Mage::getStoreConfig(self::ENABLED_KEY, $storeId);
    }
//
//    public function getEmailConfirmationUrl($email = null)
//    {
//        return $this->_getUrl('customer/account/subAccountConfirmation', array('email' => $email));
//    }

    public function hasWritePermission()
    {
        if ($subAccount = $this->isSubAccountMode()) {
            return $subAccount->hasWritePermission();
        }
        return true;
    }

    public function hasCreateOrderPermission()
    {
        if ($subAccount = $this->isSubAccountMode()) {
            return $subAccount->hasCreateOrderPermission();
        }
        return true;
    }

    public function hasNeedApprovalPermission()
    {
        if ($subAccount = $this->isSubAccountMode()) {
            return $subAccount->hasNeedApprovalPermission();
        }
        return false;
    }

    public function canViewAllOrders()
    {
        if ($subAccount = $this->isSubAccountMode()) {
            return $subAccount->canViewAllOrders();
        }
        return true;
    }

    /**
     * @return sub account model
     */
    public function isSubAccountMode()
    {
        $subAccount = Mage::getSingleton('customer/session')->getSubAccount();
        $account = Mage::getSingleton('customer/session')->getCustomer();
        $customerId = $account->getId();
        $subAcc = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($customerId, 'customer_id');
        if ($subAccount && $subAccount->getId()) {
            return $subAccount;
        } elseif (count($subAcc->getData()) > 0) {
            return $subAcc;
        }
        return false;
    }

    public function canAccessToCart()
    {
        $subAccount = Mage::getSingleton('customer/session')->getSubAccount();
        $account = Mage::getSingleton('customer/session')->getCustomer();
        $customerId = $account->getId();
        $subAcc = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($customerId, 'customer_id');
        if (($subAccount && $subAccount->getData('can_see_cart') == 1) || $subAcc->getData('can_see_cart') == 1) {
            return true;
        }
        return false;
    }

    public function haveAccessToCheckout()
    {
        $subAccount = Mage::getSingleton('customer/session')->getSubAccount();
        $account = Mage::getSingleton('customer/session')->getCustomer();
        $customerId = $account->getId();
        $subAcc = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($customerId, 'customer_id');
        if (($subAccount && $subAccount->getData('have_access_checkout') == 1) || $subAcc->getData('have_access_checkout') == 1) {
            return true;
        }
        return false;
    }

    public function isParentCustomer($id)
    {
        $subAccountData = Mage::getModel('cminds_multiuseraccounts/subAccount')->getCollection()
            ->addFieldToFilter('parent_customer_id', $id);
        if (count($subAccountData) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getSubAccountData($id)
    {
        $subAccountData = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($id);

        return $subAccountData->getData();
    }

    public function ifShareSession()
    {
        $ifShareSession = Mage::getStoreConfig('subAccount/general/if_customer_share_session');
        return $ifShareSession;
    }

    public function isSubAccount($id)
    {
        $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')
            ->load($id, 'customer_id');

        if (count($subAccount->getData()) == 0) {
            return false;
        }
        return true;
    }

    public function isQuoteApproved($quoteId)
    {
        $quote = Mage::getModel('sales/quote')->load($quoteId);

        $isApproved = $quote->getId() && $quote->getQuoteApprove() == Cminds_MultiUserAccounts_Model_SubAccount_Approvalstatuses::ASK_APPROVED;

        if ($isApproved) {
            return true;
        }
        return false;
    }

    public function hasEmailsPermission()
    {
        $permissions = Mage::getModel('cminds_multiuseraccounts/subAccount_emails')->getEmailsPermission();
        $configPermission = Mage::getStoreConfig('subAccount/general/send_copy_to_subaccount');
        return (in_array($configPermission, $permissions));
    }

    /**
     * Return bool value depends if subaccount session is emulated or not.
     *
     * @return bool
     */
    public function isSubaccountSessionEmulated()
    {
        /** @var Mage_Customer_Model_Session $customerSession */
        $customerSession = Mage::getSingleton('customer/session');

        $isEmulated = (bool)$customerSession
            ->getIsSubaccountSessionEmulated();

        return $isEmulated;
    }

    /**
     * @return array
     */
    public function getAllowedGroups()
    {
        $allowedGroup = Mage::getStoreConfig(
            'subAccount/general/customer_group_manageable'
        );
        return explode(',', $allowedGroup);
    }

    public function isTransferCartEnabled()
    {
        if ($this->ifShareSession()) {
            return false;
        }

        return (bool) Mage::getStoreConfig('subAccount/general/allow_cart_transfers');
    }
}
