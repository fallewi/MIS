<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('cminds_multiuseraccounts/subAccount'),
        'customer_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'default' => null,
            'comment' => 'Customer Id'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'),
        'parent_customer_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'default' => null,
            'comment' => 'Customer Id'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'),
        'parent_customer_firstname',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'default' => null,
            'length' => 64,
            'comment' => 'Customer Id'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'),
        'parent_customer_lastname',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'default' => null,
            'length' => 64,
            'comment' => 'Customer Id'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'),
        'parent_customer_email',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'default' => null,
            'length' => 128,
            'comment' => 'Customer Id'
        )
    );
$installer->endSetup();