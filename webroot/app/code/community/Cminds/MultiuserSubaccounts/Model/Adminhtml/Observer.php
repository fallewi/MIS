<?php

class Cminds_MultiuserSubaccounts_Model_Adminhtml_Observer {

    public function addBindedTab($observer){
        if(Mage::helper('cminds_multiusersubaccounts')->isExtraEnabled()) {
            $block = $observer->getEvent()->getBlock();

            if ($block instanceof Cminds_MultiUserAccounts_Block_Adminhtml_SubAccount_Edit_Tabs){

                $block->addTabAfter('subaccount_edit_tab_binded', array(
                    'label' => Mage::helper('catalog')->__('Binded Customers'),

                    'url'   => Mage::helper('adminhtml')->getUrl('*/subAccount/bindedGrid', array('_current' => true)),
                    'class' => 'ajax',
                ), 'general');
            }
            return $this;
        }
    }
}