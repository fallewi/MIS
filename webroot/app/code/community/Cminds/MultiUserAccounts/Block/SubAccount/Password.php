<?php

class Cminds_MultiUserAccounts_Block_SubAccount_Password extends Cminds_MultiUserAccounts_Block_SubAccount_Abstract
{
    public function getInfoBlockHtml()
    {
        $nameBlock = $this->getLayout()
            ->createBlock('cminds_multiuseraccounts/subAccount_widget_info')
            ->setObject($this->getSubAccount());

        return $nameBlock->toHtml();
    }

    public function getPostActionUrl()
    {
        return $this->getUrl('customer/account/subChangePasswordEmailPost');
    }

    public function getSubAccountMail()
    {
        $subAccount = Mage::helper('cminds_multiuseraccounts')->isSubAccountMode();
        return $subAccount->getEmail();
    }
}