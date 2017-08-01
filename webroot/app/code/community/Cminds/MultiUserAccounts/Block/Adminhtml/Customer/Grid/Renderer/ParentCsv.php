<?php

class Cminds_MultiUserAccounts_Block_Adminhtml_Customer_Grid_Renderer_ParentCsv extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData('entity_id');
        $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($value, 'customer_id');
        if (count($subAccount->getData()) > 0) {
            $parentCustomerId = $subAccount->getData('parent_customer_id');
            $parentCustomer = Mage::getModel('customer/customer')->load($parentCustomerId);

            return $parentCustomer->getName();
        } else {
            return '-';
        }
    }
}