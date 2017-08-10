<?php

class Cminds_MultiUserAccounts_Block_Checkout_Cart_Popup_Form extends Mage_Core_Block_Template
{
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('cminds_multiuseraccounts/checkout/cart/popup/form.phtml');
    }

    public function getSubAccount()
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        return $helper->isSubAccountMode();
    }

    public function getPostActionUrl()
    {
        return Mage::getUrl('multiuseraccounts/transferCart/sendCart');
    }

    public function getCartReceivers()
    {
        $receivers = new Varien_Data_Collection();
        $subAccount = $this->getSubAccount();

        if($subAccount && $subAccount->getId()) {
            $receivers = $subAccount->getCartReceivers();
        }

        return $receivers;
    }

}
