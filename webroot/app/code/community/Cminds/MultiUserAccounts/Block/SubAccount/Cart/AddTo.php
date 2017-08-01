<?php

class Cminds_MultiUserAccounts_Block_SubAccount_Cart_AddTo
    extends Cminds_MultiUserAccounts_Block_SubAccount_Abstract
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $name = Mage::registry('name');
        $sku = Mage::registry('sku');

        $items = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('name', array('like' => '%' . $name . '%'))
            ->addAttributeToFilter('sku', array('like' => '%' . $sku . '%'));
        $items->addFinalPrice();
        $this->setItems($items);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'multiuseraccounts.customer.subaccount.pager')
            ->setCollection($this->getItems());
        $this->setChild('pager', $pager);
        return $this;
    }

    public function getSearchActionUrl($quoteId)
    {
        return $this->getUrl('customer/account/addToCartView/id', array(
                'quote_id' => $quoteId,
                'subaccount_id' => $this->getSubaccountId()
            )
        );
    }

    public function getAddToCartActionUrl($quoteId)
    {
        return $this->getUrl('customer/account/addToCart');
    }

    public function getSubaccountId()
    {
        return Mage::registry('subaccount_id');
    }

}