<?php

class Cminds_MultiUserAccounts_Block_SubAccount_Cart_View extends Cminds_MultiUserAccounts_Block_SubAccount_Abstract
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('Items in Cart'));
        }
    }

    public function getSubaccountId()
    {
        return Mage::registry('subaccount_id');
    }

    public function getItems()
    {
        $subAccountId = $this->getRequest()->getParam('id');
        $subCustomer = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($subAccountId);
        $subId = $subCustomer->getData('customer_id');

        $quote = Mage::getModel('sales/quote')->getCollection()
            ->setOrder('entity_id', 'DESC')
            ->addFieldToFilter('customer_id', $subId)
            ->addFieldToFilter('is_active', 1)
            ->getFirstItem();
        return $quote;
    }

    public function hasNeedApprovalPermission($id)
    {
        $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($id);
        return $subAccount->hasNeedApprovalPermission();
    }

    public function getDeleteUrl($itemId)
    {
        return $this->getUrl(
            'multiuseraccounts/subcart/deleteitem',
            array(
                'id' => $itemId,
                'form_key' => Mage::getSingleton('core/session')->getFormKey(),
                'quote_id' => $this->getItems()->getId(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->helper('core/url')->getEncodedUrl()
            )
        );
    }

    public function getFormActionUrl()
    {
        return $this->getUrl('multiuseraccounts/subcart/updateCartPost',
            array('_secure' => $this->_isSecure()));
    }

    public function getHelper($helper = 'cminds_multiuseraccounts')
    {
        return Mage::helper($helper);
    }

    public function getAddToCartViewUrl($quoteId)
    {
        return $this->getUrl('customer/account/addToCartView/id', array(
                'quote_id' => $quoteId,
                'subaccount_id' => $this->getSubaccountId()
            )
        );
    }

}