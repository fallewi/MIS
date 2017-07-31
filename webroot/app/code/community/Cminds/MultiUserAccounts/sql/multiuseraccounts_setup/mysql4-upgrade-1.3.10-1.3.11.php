<?php
$installer=$this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn(
        $installer->getTable('cminds_multiuseraccounts/subAccount'),
        'order_amount_limit',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => false,
            'default' => 0,
            'comment' => 'Order Amount limit assigned to Sub Accounts. Could be per day/month/yer'
        )
    );
$installer->getConnection()
    ->addColumn(
        $installer->getTable('cminds_multiuseraccounts/subAccount'),
        'order_amount_limit_value',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'scale'     => 4,
            'precision' => 12,
            'nullable'  => false,
            'default'   => '0.0000',
            'comment' => 'Value of Order Amount limit assigned to Sub Accounts. Could be per day/month/yer'
        )
    );
$installer->endSetup();
