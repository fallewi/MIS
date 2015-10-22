<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

	$result = $installer->getConnection()->addColumn($installer->getTable('customer/customer_group'),'customer_group_image_url', array(
			'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
			'nullable'  => false,
	        'length'=> 255,
	        'comment' => 'Customer Group Image'
	));

	$installer->endSetup();
