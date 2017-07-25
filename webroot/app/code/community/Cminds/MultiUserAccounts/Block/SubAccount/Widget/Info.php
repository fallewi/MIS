<?php

class Cminds_MultiUserAccounts_Block_SubAccount_Widget_Info extends Mage_Customer_Block_Widget_Abstract
{
    public function _construct()
    {
        parent::_construct();

        // default template location
        $this->setTemplate('cminds_multiuseraccounts/subAccount/widget/info.phtml');
    }

    public function getPermissionOptions()
    {
        $permissions = Mage::getModel('cminds_multiuseraccounts/subAccount_permission');
        return $permissions->getAllOptions();
    }

    public function getOrderWritePermission()
    {
        return Mage::getModel('cminds_multiuseraccounts/subAccount_permission')
            ->getOrderWritePermission();
    }

    public function getNeedApprovalPermission()
    {
        $needApprovalPermission = Mage::getModel('cminds_multiuseraccounts/subAccount_permission')
            ->getNeedApprovalPermission();

        return $needApprovalPermission[0];
    }

    public function getSelectedApprover($subAccount, $approverId)
    {
        if (!$subAccount->hasAssignedApprovers()) {
            return false;
        }

        if (!in_array($approverId, $subAccount->getArrayAssignedApprovers())) {
            return false;
        }

        return true;
    }

    public function getLimitsOptions()
    {
        $limits = Mage::getModel('cminds_multiuseraccounts/subAccount_limits');
        return $limits->getAllOptions();
    }

    public function getLimitsAvailable($json = false)
    {
        $limits = Mage::getModel('cminds_multiuseraccounts/subAccount_limits');
        $allLimits = $limits->getOptionValues();

        $nonLimitedKey = array_search(
            Cminds_MultiUserAccounts_Model_SubAccount_Limits::LIMIT_NONE,
            $allLimits
        );

        if ($nonLimitedKey !== false) {
            unset($allLimits[$nonLimitedKey]);
        }
        if ($json) {
            return json_encode($allLimits);
        } else {
            return $allLimits;
        }
    }
}
