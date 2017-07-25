<?php

class Cminds_MultiUserAccounts_Block_Adminhtml_Import_index extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'cminds_multiuseraccounts';
        $this->_controller = 'adminhtml_import';
        $this->_headerText = Mage::helper('cminds_multiuseraccounts')->__('Import Accounts');
    }


    public function getHeaderText()
    {

        return Mage::helper('cminds_multiuseraccounts')->__('Import Accounts');

    }


}
