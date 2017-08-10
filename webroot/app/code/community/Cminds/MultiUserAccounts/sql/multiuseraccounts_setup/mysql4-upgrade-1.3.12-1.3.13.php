<?php
$installer=$this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('cminds_multiuseraccounts/subAccount'),
        'is_approver',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => true,
            'default' => 0,
            'comment' => 'Permission to approve other Sub Accounts Carts'
        )
    );
$installer->endSetup();