<?php
$installer = $this;

$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('sales/invoice'), 'salesforce_id', 'varchar(50)');
$installer->endSetup();