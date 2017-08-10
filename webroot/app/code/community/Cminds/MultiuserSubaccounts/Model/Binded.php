<?php

class Cminds_MultiuserSubaccounts_Model_Binded extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('cminds_multiusersubaccounts/binded', 'id');
    }

    public function isBound($subAccountId, $customerId) {
        $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($subAccountId);
        if($subAccount->getParentCustomerId() == $customerId) {
            return true;
        }

        $collection = $this->getCollection();
        $collection->addFieldToFilter('subaccount_id', array('eq' => $subAccountId));

        $customerIds = array();
        foreach ($collection as $item) {
            $customerIds[] = $item->getCustomerId();
        }
        return in_array($customerId, $customerIds);
    }
}