<?php

class Cminds_MultiUserAccounts_Block_Page_Html_Welcome
    extends Mage_Page_Block_Html_Welcome
{
    /**
     * Return block html.
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var Mage_Customer_Model_Customer $customerSession */
        $customerSession = Mage::getSingleton('customer/session');

        /** @var Cminds_MultiUserAccounts_Helper_Data $dataHelper */
        $dataHelper = Mage::helper('cminds_multiuseraccounts');

        $name = $customerSession->getCustomer()->getName();

        $subAccount = $dataHelper->isSubAccountMode();
        if ($subAccount) {
            $name = $subAccount->getName();
        }

        if (empty($this->_data['welcome'])) {
            if (Mage::isInstalled()
                && Mage::getSingleton('customer/session')->isLoggedIn()
            ) {
                $this->_data['welcome'] = $this->__(
                    'Welcome, %s!',
                    $this->escapeHtml($name)
                );
            } else {
                $this->_data['welcome'] = Mage::getStoreConfig(
                    'design/header/welcome'
                );
            }
        }

        if ($dataHelper->isSubaccountSessionEmulated() === true) {
            $this->_data['welcome'] .= $this->getRestoreSessionLink();
        }

        return $this->_data['welcome'];
    }

    /**
     * Return restore session html link.
     *
     * @return string
     */
    protected function getRestoreSessionLink()
    {
        return sprintf(
            ' (<a href="%s">%s</a>)',
            $this->getUrl('multiuseraccounts/subaccount/restore'),
            $this->__('Restore session')
        );
    }
}