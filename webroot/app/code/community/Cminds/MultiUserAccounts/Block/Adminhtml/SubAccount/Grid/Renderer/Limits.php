<?php

class Cminds_MultiUserAccounts_Block_Adminhtml_SubAccount_Grid_Renderer_Limits
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $limit = Mage::getModel('cminds_multiuseraccounts/subAccount_limits');
        $id =  $row->getData('entity_id');
        $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($id);

        if (!$subAccount->hasCreateOrderPermission()) {
            return '-';
        }

        if (!$subAccount->hasOrderLimit()) {
            return $limit->getOptionText(Cminds_MultiUserAccounts_Model_SubAccount_Limits::LIMIT_NONE);
        } else {
            return Mage::helper('cminds_multiuseraccounts')->__(
                'Limited %s per %s',
                Mage::helper('core')->currency($subAccount->getOrderAmountLimitValue(), true, false),
                $limit->getOptionText($subAccount->getOrderAmountLimit())
            );
        }
    }
}