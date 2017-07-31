<?php

class Cminds_MultiuserSubaccounts_Helper_Data extends Mage_Customer_Helper_Data
{
    public function isExtraEnabled() {
        return Mage::getStoreConfig('subAccount/extra_sub_accounts/module_enabled');
    }

    public function haveBindedCustomers()
    {
        $subAccount = Mage::helper('cminds_multiuseraccounts')->isSubAccountMode();
        $subAccountId = $subAccount->getId();
        $binded = Mage::getModel('cminds_multiusersubaccounts/binded')->getCollection()->addFieldToFilter('subaccount_id', array('eq' => $subAccountId));
        return count($binded) > 0;
    }

    public function getCustomerCity($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $address_id = $customer->getDefaultBilling();
        if ((int)$address_id){
            $address = Mage::getModel('customer/address')->load($address_id);
            return $address->getCity();
        } else {
            return '';
        }
    }
}