<?php

class Cminds_MultiUserAccounts_Block_Adminhtml_Approval_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'cminds_multiuseraccounts';
        $this->_controller = 'adminhtml_approval_list';
        $this->_headerText = $this->__('Send to approval carts');

        parent::__construct();
        $this->_removeButton('add');
    }
}