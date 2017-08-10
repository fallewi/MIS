<?php

class Cminds_MultiUserAccounts_Block_Sales_Order_Email_Approve extends Mage_Core_Block_Template
{
    public function getCartItems()
    {
        $quote = Mage::getModel('checkout/cart')->getQuote();
        return $quote;
    }

    public function getCreatedAtFormated($format)
    {
        return Mage::helper('core')->formatDate($this->getCreatedAtStoreDate(), $format, true);
    }

    public function getPriceFormatted($price){
        return Mage::helper('core')->currency($price, true, false);
    }

    public function getSubAccount()
    {
        return Mage::helper('cminds_multiuseraccounts')->isSubAccountMode();
    }

    public function getApprovers()
    {
        $subAccount = $this->getSubAccount();
        if($subAccount->hasAssignedApprovers()) {
            $approvers = Mage::getModel('cminds_multiuseraccounts/subAccount')
                ->getCollection()
                ->getAssignedApprovers($subAccount);
        } else {
            $approvers = $subAccount->getApprovers();
        }

        return $approvers;
    }
}
