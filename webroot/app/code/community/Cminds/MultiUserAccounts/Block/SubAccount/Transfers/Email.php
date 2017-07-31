<?php

class Cminds_MultiUserAccounts_Block_SubAccount_Transfers_Email
    extends Cminds_MultiUserAccounts_Block_SubAccount_Abstract
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

}