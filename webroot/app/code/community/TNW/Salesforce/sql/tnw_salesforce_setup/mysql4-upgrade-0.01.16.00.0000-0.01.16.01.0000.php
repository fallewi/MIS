<?php

$installer = $this;

$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

/* Adding Salesforce Customer Account ID to Customer object */
$setup->addAttribute('customer', 'salesforce_account_id', array(
    'label' => 'Salesforce Account ID',
    'type' => 'varchar',
    'input' => 'text',
    'visible' => false,
    'required' => false,
    'position' => 1,
));

$installer->endSetup(); 