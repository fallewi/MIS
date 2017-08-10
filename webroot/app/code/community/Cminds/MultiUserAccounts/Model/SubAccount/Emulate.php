<?php

/**
 * Cminds MultiUserAccounts subaccount emulate model.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotrek.pierzak@gmail.com>
 * @license  https://www.cminds.com/cm-magento-extentions-license-details CreativeMinds Magento Extensions License
 * @link     https://www.cminds.com/ecommerce-extensions-store/magento-multi-user-account-extension
 */
class Cminds_MultiUserAccounts_Model_SubAccount_Emulate
{
    /**
     * Subaccount id.
     *
     * @var int|null
     */
    protected $subaccountId;

    /**
     * Subaccount model.
     *
     * @var Cminds_MultiUserAccounts_Model_SubAccount|null
     */
    protected $subaccountModel;

    /**
     * Data helper object.
     *
     * @var Cminds_MultiUserAccounts_Helper_Data
     */
    protected $dataHelper;

    /**
     * Object initialization.
     */
    public function __construct()
    {
        $this->dataHelper = Mage::helper('cminds_multiuseraccounts');
    }

    /**
     * Data helper getter.
     *
     * @return Cminds_MultiUserAccounts_Helper_Data|Mage_Core_Helper_Abstract
     */
    protected function getDataHelper()
    {
        return $this->dataHelper;
    }

    /**
     * Subaccount id setter.
     *
     * @param int $subaccountId Subaccount id.
     *
     * @return Cminds_MultiUserAccounts_Model_SubAccount_Emulate
     */
    public function setSubaccountId($subaccountId)
    {
        $this->subaccountId = $subaccountId;

        return $this;
    }

    /**
     * Subaccount id getter.
     *
     * @return int|null
     */
    public function getSubaccountId()
    {
        return $this->subaccountId;
    }

    /**
     * Subaccount model getter.
     *
     * @return Cminds_MultiUserAccounts_Model_SubAccount|null
     */
    public function getSubaccount()
    {
        return $this->subaccountModel;
    }

    /**
     * Emulate subaccount session.
     *
     * @return Cminds_MultiUserAccounts_Model_SubAccount_Emulate
     */
    public function emulate()
    {
        $this
            ->validate()
            ->clearSession()
            ->emulateSession();

        return $this;
    }

    /**
     * Restore customer session.
     *
     * @return Cminds_MultiUserAccounts_Model_SubAccount_Emulate
     */
    public function restore()
    {
        $this
            ->clearSession()
            ->restoreSession();

        return $this;
    }

    /**
     * Validate data.
     *
     * @return Cminds_MultiUserAccounts_Model_SubAccount_Emulate
     * @throws Mage_Core_Exception
     */
    protected function validate()
    {
        $subaccountId = $this->getSubaccountId();
        if (!is_int($subaccountId)) {
            throw new Mage_Core_Exception(
                $this->dataHelper->__('Subaccount id has been not set.')
            );
        }

        $subaccountModel = Mage::getModel('cminds_multiuseraccounts/subAccount')
            ->load($subaccountId);
        if (!$subaccountModel->getId()) {
            throw new Mage_Core_Exception(
                $this->dataHelper->__('Subaccount entity does not exists.')
            );
        }

        if($this->dataHelper->isSubAccountMode()) {
            throw new Mage_Core_Exception(
                $this->dataHelper->__('User not Allowed to Emulate Session')
            );
        }

        $this->subaccountModel = $subaccountModel;

        return $this;
    }

    /**
     * Return current website id.
     *
     * @return int
     * @throws Mage_Core_Exception
     */
    protected function getWebsiteId()
    {
        return Mage::app()->getWebsite()->getId();
    }

    /**
     * Emulate session.
     *
     * @return Cminds_MultiUserAccounts_Model_SubAccount_Emulate
     */
    protected function emulateSession()
    {
        $customerSession = Mage::getSingleton('customer/session');

        if ($this->isSharedSessionEnabled() === true) {
            $customerSession->setSubAccount($this->getSubaccount());
        } else {
            $websiteId = $this->getWebsiteId();

            $currentCustomerModel = $customerSession->getCustomer();

            $customerModel = Mage::getModel('customer/customer')
                ->setWebsiteId($websiteId)
                ->loadByEmail(
                    $this->getSubaccount()->getEmail()
                );
            $customerSession
                ->setCustomerAsLoggedIn($customerModel)
                ->setParentCustomerId($currentCustomerModel->getId());
        }

        $customerSession->setIsSubaccountSessionEmulated(true);

        return $this;
    }

    /**
     * Restore session.
     *
     * @return Cminds_MultiUserAccounts_Model_SubAccount_Emulate
     */
    protected function restoreSession()
    {
        $customerSession = Mage::getSingleton('customer/session');

        if ($this->isSharedSessionEnabled() === true) {
            $customerSession->removeSubAccount();
        } else {
            $customerModel = Mage::getModel('customer/customer')
                ->load($customerSession->getParentCustomerId());

            $customerSession
                ->setCustomerAsLoggedIn($customerModel)
                ->setParentCustomerId(null);
        }

        $customerSession->setIsSubaccountSessionEmulated(false);

        return $this;
    }

    /**
     * Clear session between emulating and restoring session.
     *
     * @return Cminds_MultiUserAccounts_Model_SubAccount_Emulate
     */
    protected function clearSession()
    {
        $customerSession = Mage::getSingleton('customer/session');

        $customerSession
            ->setCustomerId(null)
            ->setCustomerGroupId(null);

        $checkoutSession = Mage::getSingleton('checkout/session');

        $quote = $checkoutSession
            ->setLoadInactive()
            ->getQuote();
        if ($quote->getIsActive() && $quote->getCustomerId()) {
            $checkoutSession
                ->setCustomer(null)
                ->unsetAll();
        } else {
            $quote
                ->setIsActive(true)
                ->setIsPersistent(false)
                ->setCustomerId(null)
                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        }

        return $this;
    }

    /**
     * Return bool value if shared session is enabled or not.
     *
     * @return bool
     */
    protected function isSharedSessionEnabled()
    {
        return (bool)$this->getDataHelper()->ifShareSession();
    }
}
