<?php

class Cminds_MultiUserAccounts_Block_SubAccount_Transfers_View extends Cminds_MultiUserAccounts_Block_SubAccount_Abstract
{

    public function getTransfer()
    {
        return Mage::registry('transfer');
    }

    public function getQuote()
    {
        $transfer = $this->getTransfer();
        $quoteId = $transfer->getQuoteId();
        return Mage::getModel('sales/quote')->load($quoteId);
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/transferslist');
    }

    public function getTransferUrl()
    {
        return $this->getUrl(
            'multiuseraccounts/transferCart/transfer',
            array('id' => $this->getTransfer()->getId())
        );
    }

}