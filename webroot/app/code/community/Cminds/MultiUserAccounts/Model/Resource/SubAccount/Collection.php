<?php

class Cminds_MultiUserAccounts_Model_Resource_SubAccount_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('cminds_multiuseraccounts/subAccount');
    }

    public function getSubWithOrderPermission($parentId)
    {
        $options = array();
        $this->addFieldToFilter('parent_customer_id', $parentId);
        $this->addFieldToFilter('permission',
            array(
                array('eq' => Cminds_MultiUserAccounts_Model_SubAccount_Permission::PERMISSION_ORDER),
                array('eq' => Cminds_MultiUserAccounts_Model_SubAccount_Permission::PERMISSION_ORDER_WRITE),
            )
        );
        $data = $this->getData();
        foreach ($data as $cats) {
            $options[] = array(
                'value' => $cats['entity_id'],
                'label' => $cats['email']
            );
        }
        if (count($options) > 0 || count($options) == 0) {
            array_unshift($options, array('value' => '', 'label' => '---None---'));
        }
        return $options;
    }

    public function getParentIds()
    {
        $parentIds = array();
        foreach ($this as $subAccount) {
            $parentIds[] = $subAccount->getParentCustomerId();
        }
        return $parentIds;
    }

    public function getCustomerIds()
    {
        $customerIds = array();
        foreach ($this as $subAccount) {
            $customerIds[] = $subAccount->getCustomerId();
        }
        return $customerIds;
    }

    public function filterByParentId($id)
    {
        $this->addFieldToFilter('parent_customer_id', $id);

        return $this;
    }

    public function filterById($id = false, $condition = null)
    {
        $this->addFieldToFilter('entity_id', array($condition => $id));

        return $this;
    }

    public function filterByApprovers($condition)
    {
        $this->addFieldToFilter('is_approver', array($condition => 1));

        return $this;
    }

    public function getAssignedApprovers($subAccount)
    {
        $this->addFieldToFilter('entity_id', array('in' => $subAccount->getArrayAssignedApprovers()));

        return $this;
    }

    public function filterByModifyPermission()
    {
        $this->addFieldToFilter('permission', array('eq' => Cminds_MultiUserAccounts_Model_SubAccount_Permission::PERMISSION_ORDER_WRITE));

        return $this;
    }

    public function filterByOrderCreatePermission()
    {
        $permission = Mage::getModel('cminds_multiuseraccounts/subAccount_permission');
        $this->addFieldToFilter('permission', array('in' => $permission->getOrderCreationPermission()));

        return $this;
    }
}