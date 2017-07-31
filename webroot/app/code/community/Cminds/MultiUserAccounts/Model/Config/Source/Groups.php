<?php

/**
 * Class Cminds_MultiUserAccounts_Model_Config_Source_Groups
 */
class Cminds_MultiUserAccounts_Model_Config_Source_Groups
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $customer_group = new Mage_Customer_Model_Group();
        $allGroups = $customer_group->getCollection();
        $allSet = array();

        foreach ($allGroups AS $group) {
            $allSet[] = array('value' => $group->getCustomerGroupId(), 'label' => $group->getCustomerGroupCode());
        }

        return $allSet;

    }
}