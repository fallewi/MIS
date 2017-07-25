<?php

class Cminds_MultiUserAccounts_Model_SubAccount extends Mage_Customer_Model_Customer
{
    const XML_PATH_IS_CONFIRM = 'subAccount/create_subAccount/confirm';
    const KEY_SIGN = '-SUB';
    protected $_eventPrefix = 'subaccount';
    private static $_isConfirmationRequired;

    function _construct()
    {
        $this->_init('cminds_multiuseraccounts/subAccount');
    }

    // We need $login for method signature compatibility
    public function authenticate($login, $password)
    {
        if (!$this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        if (!$this->validatePassword($password)) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid login or password.'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
            );
        }

        return true;
    }

    public function getSubAccounts(Mage_Customer_Model_Customer $customer)
    {
        /** @var  $collection Cminds_MultiUserAccounts_Model_Resource_SubAccount_Collection */
        $collection = $this->getCollection();
        $collection->addFieldToFilter('parent_customer_id', $customer->getId());

        return $collection;
    }

    public function getName()
    {
        $name = '';

        $name .= $this->getFirstname();
        $name .= ' ' . $this->getLastname();

        return $name;
    }

    public function getPermissionLabel()
    {
        $permission = '';
        $permission = Mage::getModel('cminds_multiuseraccounts/subAccount_permission')->getOptionText($this->getPermission());

        return $permission;
    }

    public function validate()
    {
        $errors = array();
        if (!Zend_Validate::is(trim($this->getFirstname()), 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The first name cannot be empty.');
        }

        if (!Zend_Validate::is(trim($this->getLastname()), 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The last name cannot be empty.');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('customer')->__('Invalid email address "%s".', $this->getEmail());
        }

        if (!Zend_Validate::is($this->getPermission(), 'Int')) {
            $errors[] = Mage::helper('customer')->__('Invalid permissions "%s".', $this->getPermission());
        }
        if (!Zend_Validate::is($this->getParentCustomerId(), 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('Invalid main account "%s".', $this->getParentCustomerId());
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Zend_Validate::is($password, 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The password cannot be empty.');
        }
        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(6))) {
            $errors[] = Mage::helper('customer')->__('The minimum password length is %s', 6);
        }
        $confirmation = $this->getPasswordConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('customer')->__('Please make sure your passwords match.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    // Required to use the SubAccount XML_PATH_IS_CONFIRM KEY
    public function isConfirmationRequired()
    {
        if (self::$_isConfirmationRequired === null) {
            $storeId = $this->getStoreId() ? $this->getStoreId() : null;
            self::$_isConfirmationRequired = (bool)Mage::getStoreConfig(self::XML_PATH_IS_CONFIRM, $storeId);
        }

        return self::$_isConfirmationRequired;
    }

    // Remove frontend check for admin area
    protected function _beforeDelete()
    {
        Mage::dispatchEvent('model_delete_before', array('object' => $this));
        Mage::dispatchEvent($this->_eventPrefix . '_delete_before', $this->_getEventData());
        $this->cleanModelCache();
        return $this;
    }

    public function hasWritePermission()
    {
        $permissions = Mage::getModel('cminds_multiuseraccounts/subAccount_permission')->getWritePermission();
        return (in_array($this->getPermission(), $permissions));
    }

    public function hasCreateOrderPermission()
    {
        $permissions = Mage::getModel('cminds_multiuseraccounts/subAccount_permission')->getOrderCreationPermission();
        return (in_array($this->getPermission(), $permissions));
    }

    public function hasNeedApprovalPermission()
    {
        $permissions = Mage::getModel('cminds_multiuseraccounts/subAccount_permission')->getNeedApprovalPermission();
        return (in_array($this->getPermission(), $permissions));
    }

    public function canViewAllOrders()
    {
        return (1 == $this->getViewAllOrders()) ? true : false;
    }

    /**
     * Generate random confirmation key
     *
     * @return string
     */
    public function getRandomConfirmationKey()
    {
        return parent::getRandomConfirmationKey() . self::KEY_SIGN;
    }

    /**
     * Change reset password link token
     *
     * Stores new reset password link token and its creation time
     *
     * @param Mage_Customer_Model_Customer $newResetPasswordLinkToken
     * @param string $newResetPasswordLinkToken
     * @return Mage_Customer_Model_Resource_Customer
     */
    public function changeResetPasswordLinkToken($newResetPasswordLinkToken)
    {
        if (is_string($newResetPasswordLinkToken) && !empty($newResetPasswordLinkToken)) {
            $newResetPasswordLinkToken = $newResetPasswordLinkToken . self::KEY_SIGN;
            $this->setRpToken($newResetPasswordLinkToken);
            $currentDate = Varien_Date::now();
            $this->setRpTokenCreatedAt($currentDate);
            $this->save();
        }
        return $this;
    }

    public function sendPasswordReminderEmail()
    {
        $this->setName($this->getFirstName() . ' ' . $this->getLastName());

        parent::sendPasswordReminderEmail();

        return $this;
    }

    public function canBeApprover()
    {
        $orderWritePermission = Mage::getModel('cminds_multiuseraccounts/subAccount_permission')
            ->getOrderWritePermission();

        if($this->getPermission() == $orderWritePermission) {
            return true;
        }

        return false;
    }

    public function isApprover()
    {
        if(!$this->canBeApprover()) {
            return false;
        }

        return $this->getIsApprover();
    }

    public function canApprove($subAccountId)
    {
        $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')
            ->load($subAccountId);
        if(!$subAccountId) {
            return false;
        }

        if(!$this->isApprover()) {
            return false;
        }

        if(!in_array($subAccountId, $this->getOtherSubAccounts(true))) {
            return false;
        }

        if(
            $subAccount->hasAssignedApprovers()
            && !in_array($this->getId(), $subAccount->getArrayAssignedApprovers()))
        {
            return false;
        }

        return true;
    }

    public function getOtherSubAccounts($returnIds = false)
    {
        $collection = $this->getCollection();
        $collection
            ->filterByParentId($this->getParentCustomerId())
            ->filterById($this->getId(), 'neq');

        if(!$returnIds) {
            return $collection;
        }

        $ids = array();
        foreach ($collection as $subAccount) {
            $ids[] = $subAccount->getId();
        }

        return $ids;
    }

    public function getApprovers()
    {
        $collection = $this->getOtherSubAccounts();
        $collection->filterByApprovers('eq');
        $collection->filterByModifyPermission();

        return $collection;
    }

    /**
     * @return bool
     */
    public function hasAssignedApprovers()
    {

        if(count($this->getArrayAssignedApprovers()) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getArrayAssignedApprovers()
    {
        $assignedApprovers =  array();

        if ($this->getAssignedApprovers()) {
            $assignedApprovers = unserialize($this->getAssignedApprovers());
        }

        return $assignedApprovers;
    }

    public function hasOrderLimit()
    {
        $limit = $this->getOrderAmountLimit();

        if ($limit == Cminds_MultiUserAccounts_Model_SubAccount_Limits::LIMIT_NONE) {
            return false;
        }

        return true;
    }

    /**
     * @param $order
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function validateOrderLimits($quote)
    {
        $period = '';
        switch ($this->getOrderAmountLimit()) {
            case Cminds_MultiUserAccounts_Model_SubAccount_Limits::LIMIT_NONE:
                return true;
                break;

            case Cminds_MultiUserAccounts_Model_SubAccount_Limits::LIMIT_DAY:
                $period = 'Day';
                break;

            case Cminds_MultiUserAccounts_Model_SubAccount_Limits::LIMIT_MONTH:
                $period = 'Month';
                break;

            case Cminds_MultiUserAccounts_Model_SubAccount_Limits::LIMIT_YEAR:
                $period = 'Year';
                break;

            case Cminds_MultiUserAccounts_Model_SubAccount_Limits::LIMIT_ORDER:
                return $this->checkIfLimitReached($quote->getGrandTotal());
                break;
        }

        $amount = $this->getOrderAmountByDate($period);

        if ($quote) {
            $amount += $quote->getGrandTotal();
        }

        return $this->checkIfLimitReached($amount);
    }

    /**
     * @param $period string day/month/year
     * @return mixed
     */
    public function getOrderAmountByDate($period)
    {

        $collection = Mage::getResourceModel('cminds_multiuseraccounts/sales_order_collection');
        return $collection->getOrderAmountByDate($period, $this->getId());
    }

    /**
     * @param $amount
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function checkIfLimitReached($amount)
    {
        if ($amount > $this->getOrderAmountLimitValue()) {
            throw Mage::exception('Mage_Core', 'Your order amount limit has been reached');
        }

        return true;
    }

    public function getCartReceivers()
    {
        $otherSubAccounts = $this->getOtherSubAccounts(true);

        $collection = $this->getCollection();
        $collection
            ->filterById($otherSubAccounts, 'in')
            ->filterByOrderCreatePermission();

        return $collection;
    }
}
