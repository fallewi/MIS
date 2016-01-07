<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute('customer','legacy_customer_id',array(
    'type'=>'int',
    'backend_type'=>'text',
    'label'   => "Legacy Customer ID",
    'global' => 1,
    'visible' => 0,
    'required' => 0,
    'user_defined' => 0,
    'default' => '',
    'visible_on_front' => 0,
));

   Mage::getSingleton('eav/config')
        ->getAttribute('customer', 'legacy_customer_id')
        ->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit'))
        ->save();

$installer->endSetup();


