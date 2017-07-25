<?php

/**
 * Class Cminds_MultiUserAccounts_Model_Resource_Customer_Collection
 */
class Cminds_MultiUserAccounts_Model_Resource_Customer_Collection
    extends Mage_Customer_Model_Resource_Customer_Collection
{

    /**
     * @return $this
     */
    public function filterByMasterAccounts()
    {
        $subCollection = Mage::getModel('cminds_multiuseraccounts/subAccount')
            ->getCollection();
        $parentIds = $subCollection->getParentIds();

        $this->addAttributeToFilter(
            'entity_id',
            array(
                'nin' => $parentIds
            )
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function filterByExistedSubAccounts()
    {
        $subCollection = Mage::getModel('cminds_multiuseraccounts/subAccount')
            ->getCollection();
        $customerIds = $subCollection->getCustomerIds();

        $this->addAttributeToFilter(
            'entity_id',
            array(
                'nin' => $customerIds
            )
        );

        return $this;
    }

    /**
     * @param $groupId
     * @return $this
     */
    public function filterByGroupId($groupId)
    {
        $this->addAttributeToFilter('group_id', array('eq' => $groupId));
        return $this;
    }

    /**
     * @param $websiteId
     * @return $this
     */
    public function filterByWebsiteId($websiteId)
    {
        $this->addAttributeToFilter('website_id', array('eq' => $websiteId));
        return $this;
    }

}