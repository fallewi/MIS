<?php

class Cminds_MultiUserAccounts_Block_SubAccount_Transfers extends Cminds_MultiUserAccounts_Block_SubAccount_Abstract
{
    public function __construct()
    {
        parent::__construct();

        $items = $this->prepareItems();

        $this->setItems($items);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'multiuseraccounts.customer.subaccount.carts.transfered.pager')
            ->setCollection($this->getItems());
        $this->setChild('pager', $pager);
        $this->getItems()->load();
        return $this;
    }

    public function prepareItems()
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $items = new Varien_Data_Collection();
        $subAccount = $helper->isSubAccountMode();

        if($helper->isEnabled()
            && $helper->isTransferCartEnabled()
            && $subAccount
            && $subAccount->hasCreateOrderPermission()
        ) {
            $items = Mage::getModel('cminds_multiuseraccounts/transfer')->getCollection();
            $items->addFieldToFilter('subaccount_id', array('eq' => $subAccount->getId()))
                ->addFieldToFilter('was_transfered', array('neq' => 1));
        }

        return $items;
    }

    public function getSubaccount($id)
    {
        return Mage::getModel('cminds_multiuseraccounts/subAccount')->load($id);
    }

    public function getQtyItems($item)
    {
        $quote = Mage::getModel('sales/quote')->load($item->getQuoteId());

        return $quote->getItemsQty();
    }

    public function getViewCartUrl($item)
    {
        return Mage::getUrl(
            'customer/account/transferview',
            array('id' => $item->getId())
        );
    }

}