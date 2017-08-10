<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('sales/quote'),
        'quote_approve',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => false,
            'default' => 1,
            'comment' => 'Approval of quote'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('cminds_multiuseraccounts/subAccount'),
        'get_order_email',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'default' => 1,
            'comment' => 'Permission to get rder emails'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('cminds_multiuseraccounts/subAccount'),
        'get_order_invoice',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'default' => 1,
            'comment' => 'Permission to get invoice emails'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('cminds_multiuseraccounts/subAccount'),
        'get_order_shipment',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'default' => 1,
            'comment' => 'Permission to get shipment emails'
        )
    );

$installer->endSetup();