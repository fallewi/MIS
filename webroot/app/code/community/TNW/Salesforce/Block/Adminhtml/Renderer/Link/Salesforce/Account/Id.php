<?php

class TNW_Salesforce_Block_Adminhtml_Renderer_Link_Salesforce_Account_Id extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $_field = $row->getData('salesforce_account_id');
        return Mage::helper('tnw_salesforce/salesforce_abstract')->generateLinkToSalesforce($_field);
    }
}