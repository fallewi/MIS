<?php

/**
 * @author CreativeMindsSolutions
 */
class Cminds_MultiUserAccounts_Block_Customer_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{
    protected function _beforeToHtml()
    {
        $this->_addManageUserNavigation();
        $this->addTransferCartsNavigation();
        return $this;
    }

    public function removeLink($name)
    {
        unset($this->_links[$name]);
        return $this;
    }

    public function removeLinkByName($name)
    {
        unset($this->_links[$name]);
        return $this;
    }

    protected function _addManageUserNavigation()
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $customer = Mage::getSingleton('customer/session')
            ->getCustomer();

        if ($this->validateCustomer($customer)) {

            $this->addLink(
                'sub_account',
                'customer/account/subAccount',
                $this->__('Manage Users')
            );
        }

        if ($helper->isEnabled() && $helper->isSubAccountMode()) {
            $this->addLink(
                'sub_account_password',
                'customer/account/subChangePassword',
                $this->__('Change Password/Email')
            );
        }

        if (Mage::getConfig()->getModuleConfig('Cminds_MultiuserSubaccounts')->is('active', 'true')) {
            if ($helper->isEnabled() && $helper->isSubAccountMode()) {
                if (
                    Mage::helper('cminds_multiusersubaccounts')->haveBindedCustomers()
                    && Mage::helper('cminds_multiusersubaccounts')->isExtraEnabled()
                ) {
                    $this->addLink('sub_account_binded', 'customer/account/binded', $this->__('Binded Customers'));
                }
            }
        }
    }

    public function addLink($name, $path, $label, $urlParams = array())
    {
        if (Mage::getConfig()->getModuleConfig('Cminds_Marketplace')->is('active', 'true')) {
            $configLabelName = Mage::getStoreConfig('supplierfrontendproductuploader_catalog/supplierfrontendproductuploader_presentation/account_page_label');

            if ($name == 'supplierfrontendproductuploader') {
                if (!Mage::helper('supplierfrontendproductuploader')->hasAccess() || !Mage::helper('supplierfrontendproductuploader')->isEnabled()) {
                    return $this;
                }

                if ($configLabelName != '') {
                    $label = $configLabelName;
                }
            }

            if ($name == 'marketplace_supplier_rate' || $name == 'marketplace_supplier_rates') {
                if (!Mage::helper('supplierfrontendproductuploader')->isEnabled()) {
                    return $this;
                }

                if (!Mage::getStoreConfig('marketplace_configuration/presentation/supplier_rating')) {
                    return $this;
                }
            }
        }

        $this->_links[$name] = new Varien_Object(array(
            'name' => $name,
            'path' => $path,
            'label' => $label,
            'url' => $this->getUrl($path, $urlParams),
        ));

        return $this;
    }

    public function validateCustomer($customer)
    {
        $helper = Mage::helper('cminds_multiuseraccounts');

        if(!$helper->isEnabled()) {
            return false;
        }

        if(!$customer->canManageUsers()) {
            return false;
        }

        $subAccount = $helper->isSubAccountMode();
        if($subAccount && $subAccount->getId()) {
            $isApprover = $subAccount->isApprover();
            return $isApprover;
        }

        return true;
    }

    public function addTransferCartsNavigation()
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $subAccount = $helper->isSubAccountMode();
        if ($helper->isEnabled()
            && $helper->isTransferCartEnabled()
            && $subAccount
            && $subAccount->hasCreateOrderPermission()
        ) {
            $this->addLink(
                'transfer_cart',
                'customer/account/transferslist',
                $helper->__('Transfered Carts')
            );
        }
    }
}