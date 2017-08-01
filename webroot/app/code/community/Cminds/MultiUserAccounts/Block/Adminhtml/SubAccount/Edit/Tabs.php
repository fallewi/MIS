<?php

class Cminds_MultiUserAccounts_Block_Adminhtml_SubAccount_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Constructs current object
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('cminds_multiuseraccounts')->__('Sub Account'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label' => Mage::helper('cminds_multiuseraccounts')->__('General'),
            'content' => $this->getLayout()->createBlock('cminds_multiuseraccounts/adminhtml_subAccount_edit_tab_general')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}