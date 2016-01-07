<?php

class BlueAcorn_Legacy_Model_Resource_Order_Collection extends Mage_Sales_Model_Resource_Order_Collection
{
    protected $_eventPrefix    = 'blueacorn_legacy_sales_order_collection';
    protected $_eventObject    = 'order_collection';

    protected function _construct()
    {
        $this->_init('blueacorn_legacy/order');
        $this
            ->addFilterToMap('entity_id', 'main_table.entity_id')
            ->addFilterToMap('customer_id', 'main_table.customer_id')
            ->addFilterToMap('quote_address_id', 'main_table.quote_address_id');
    }

    /**
     * Join table sales_flat_order_address to select for billing and shipping order addresses.
     * Create correlation map
     *
     * @return BlueAcorn_Legacy_Model_Resource_Order_Collection
     */
    protected function _addAddressFields()
    {
        $billingAliasName = 'billing_o_a';
        $shippingAliasName = 'shipping_o_a';
        $joinTable = $this->getTable('blueacorn_legacy/order_address');

        $this
            ->addFilterToMap('billing_firstname', $billingAliasName . '.firstname')
            ->addFilterToMap('billing_lastname', $billingAliasName . '.lastname')
            ->addFilterToMap('billing_telephone', $billingAliasName . '.telephone')
            ->addFilterToMap('billing_postcode', $billingAliasName . '.postcode')

            ->addFilterToMap('shipping_firstname', $shippingAliasName . '.firstname')
            ->addFilterToMap('shipping_lastname', $shippingAliasName . '.lastname')
            ->addFilterToMap('shipping_telephone', $shippingAliasName . '.telephone')
            ->addFilterToMap('shipping_postcode', $shippingAliasName . '.postcode');

        $this
            ->getSelect()
            ->joinLeft(
                array($billingAliasName => $joinTable),
                "(main_table.entity_id = {$billingAliasName}.parent_id"
                . " AND {$billingAliasName}.address_type = 'billing')",
                array(
                    $billingAliasName . '.firstname',
                    $billingAliasName . '.lastname',
                    $billingAliasName . '.telephone',
                    $billingAliasName . '.postcode'
                )
            )
            ->joinLeft(
                array($shippingAliasName => $joinTable),
                "(main_table.entity_id = {$shippingAliasName}.parent_id"
                . " AND {$shippingAliasName}.address_type = 'shipping')",
                array(
                    $shippingAliasName . '.firstname',
                    $shippingAliasName . '.lastname',
                    $shippingAliasName . '.telephone',
                    $shippingAliasName . '.postcode'
                )
            );
        Mage::getResourceHelper('core')->prepareColumnsList($this->getSelect());
        return $this;
    }

    /**
     * Add filter by specified billing agreements
     *
     * @param int|array $agreements
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function addBillingAgreementsFilter($agreements)
    {
        $agreements = (is_array($agreements)) ? $agreements : array($agreements);
        $this->getSelect()
            ->joinInner(
                array('sbao' => $this->getTable('sales/billing_agreement_order')),
                'main_table.entity_id = sbao.order_id',
                array())
            ->where('sbao.agreement_id IN(?)', $agreements);
        return $this;
    }

    /**
     * Add filter by specified recurring profile id(s)
     *
     * @param array|int $ids
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function addRecurringProfilesFilter($ids)
    {
        $ids = (is_array($ids)) ? $ids : array($ids);
        $this->getSelect()
            ->joinInner(
                array('srpo' => $this->getTable('sales/recurring_profile_order')),
                'main_table.entity_id = srpo.order_id',
                array())
            ->where('srpo.profile_id IN(?)', $ids);
        return $this;
    }
}