<?php

class Cminds_MultiUserAccounts_Block_Checkout_Cart_Popup extends Mage_Core_Block_Template
{
    protected function _construct() {
        parent::_construct();

        if($this->validateSubUser()
            && Mage::helper('checkout/cart')->getItemsCount() > 0
            && Mage::helper('cminds_multiuseraccounts')->isTransferCartEnabled()
        ) {
            $this->setTemplate('cminds_multiuseraccounts/checkout/cart/popup.phtml');
        }
    }

    public function validateSubUser()
    {
        $helper = Mage::helper('cminds_multiuseraccounts');

        if(!$helper->isEnabled()) {
            return false;
        }

        if(!$helper->isSubAccountMode()) {
            return false;
        }

        return true;
    }

}
