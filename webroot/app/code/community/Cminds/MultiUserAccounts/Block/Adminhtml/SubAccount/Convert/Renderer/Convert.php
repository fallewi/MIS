<?php

class Cminds_MultiUserAccounts_Block_Adminhtml_SubAccount_Convert_Renderer_Convert
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $customerId = $row->getData('entity_id');
        $message = $helper->__(
            'Are you sure you want to convert %s %s to Sub Account? Please remember this process cannot be revert.',
            $row->getData('firstname'),
            $row->getData('lastname')
        );

        return '<a onclick="confirmSetLocation(\'' . $message . '\',\''
            . $this->getUrl(
                '*/subAccount/convertCustomer',
                array(
                    'current_customer_id' => $this->getRequest()->getParam('id'),
                    'converted_customer_id' => $customerId
                )
            )
            . '\')">' . $helper->__('Convert') . '</a>';
    }
}