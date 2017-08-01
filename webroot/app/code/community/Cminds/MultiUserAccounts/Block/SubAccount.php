<?php

class Cminds_MultiUserAccounts_Block_SubAccount extends Mage_Core_Block_Template
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $items = $this->prepareItems();
        $items->setOrder('email', 'asc');

        $this->setItems($items);
    }

    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getSubAccounts()
    {
        return Mage::getModel('cminds_multiuseraccounts/subAccount')->getSubAccounts($this->getCustomer());
    }

    public function getAddSubAccountUrl()
    {
        return $this->getUrl('customer/account/addSubAccount');
    }

    public function getDeleteSubAccountUrl($id)
    {
        return $this->getUrl('customer/account/deleteSubAccount', array('id' => $id));
    }

    public function getEditSubAccountUrl($id)
    {
        return $this->getUrl('customer/account/editSubAccount/id', array('id' => $id));
    }

    /**
     * Get back url in account dashboard
     *
     * This method is copypasted in:
     * Mage_Wishlist_Block_Customer_Wishlist  - because of strange inheritance
     * Mage_Customer_Block_Address_Book - because of secure url
     *
     * @return string
     */
    public function getBackUrl()
    {
        // the RefererUrl must be set in appropriate controller
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/subAccount');
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'multiuseraccounts.customer.subaccount.pager')
            ->setCollection($this->getItems());
        $this->setChild('pager', $pager);
        $this->getItems()->load();
        return $this;
    }

    public function getShowCartItemUrl($id)
    {
        return $this->getUrl('customer/account/showCartItem/id', array('id' => $id));
    }

    public function hasNeedApprovalPermission($id)
    {
        $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($id);
        return $subAccount->hasNeedApprovalPermission();
    }

    public function isQuoteApproved($subAccountId)
    {
        $subCustomer = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($subAccountId);
        $subId = $subCustomer->getData('customer_id');

        $quote = Mage::getModel('sales/quote')->getCollection()
            ->setOrder('entity_id', 'DESC')
            ->addFieldToFilter('customer_id', $subId)
            ->addFieldToFilter('is_active', 1)
            ->getFirstItem();
        return Mage::helper('cminds_multiuseraccounts')->isQuoteApproved($quote->getId());
    }

    /**
     * Return login as subaccount url.
     *
     * @param int $subaccountId Subaccount id.
     *
     * @return string
     */
    public function getLoginAsSubaccountUrl($subaccountId)
    {
        return $this->getUrl(
            'multiuseraccounts/subaccount/emulate',
            array('id' => $subaccountId)
        );
    }

    public function prepareItems()
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $items = new Varien_Data_Collection();

        if (!$helper->isSubAccountMode()) {
            $items = Mage::getModel('cminds_multiuseraccounts/subAccount')
                ->getSubAccounts($this->getCustomer());
        }

        $subAccount = $helper->isSubAccountMode();
        if ($subAccount && $subAccount->isApprover()) {
            $items = $subAccount->getOtherSubAccounts();
        }

        return $items;
    }

    public function permissionsArray()
    {

        return array(
            'view_all_orders',
            'can_see_cart',
            'have_access_checkout',
            'get_order_email',
            'get_order_invoice',
            'get_order_shipment',
            'is_approver'
        );
    }

    public function getSortedPermissions($subAccount)
    {
        $permissionsArray = $this->permissionsArray();

        $groupedPermissions = array();

        foreach ($permissionsArray as $permission) {
            if ($subAccount->getData($permission) == "1") {
                $groupedPermissions[1][] = $permission;
            } else {
                $groupedPermissions[0][] = $permission;
            }
        }
        $sortedPermissions = array();
        if (isset($groupedPermissions[0]) && isset($groupedPermissions[1])) {
            $sortedPermissions = array_merge($groupedPermissions[1], $groupedPermissions[0]);
        } elseif (isset($groupedPermissions[0])) {
            $sortedPermissions = $groupedPermissions[0];
        } elseif (isset($groupedPermissions[1])) {
            $sortedPermissions = $groupedPermissions[1];
        }

        return $sortedPermissions;
    }

    public function getPermissionLabelByCode($code)
    {

        $permissionArray =  array(
            'view_all_orders' => 'View All Orders',
            'can_see_cart' => 'Can See to Cart Page',
            'have_access_checkout' => 'Have Access to Checkout',
            'get_order_email' => 'Get order emails',
            'get_order_invoice' => 'Get invoice emails',
            'get_order_shipment' => 'Get shipment emails',
            'is_approver' => 'Can approve carts'
        );

        $label = '';
        if (isset($permissionArray[$code])) {
            $label = $permissionArray[$code];
        }

        return $label;
    }

    public function getLimitOptionText($id)
    {
        $limits = Mage::getModel('cminds_multiuseraccounts/subAccount_limits');

        return $limits->getOptionText($id);
    }

    /**
     * @param $subAccount
     * @return string
     */
    public function getBalanceButton($subAccount)
    {
        if ($subAccount->hasOrderLimit()
            &&
            $subAccount->getOrderAmountLimit()
            == Cminds_MultiUserAccounts_Model_SubAccount_Limits::LIMIT_ORDER
        ) {
            return '';
        }

        return '<span data-subaccount-id="'. $subAccount->getId()
        .'" style="cursor: pointer; color: #3399cc;" onclick="checkOrderBalance('
        . $subAccount->getId() . ')">Check Balance</a>';
    }
}
