<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('cminds_multiuseraccounts/subAccount'),
        'can_see_cart',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'default' => '0',
            'comment' => 'Permission to see cart page'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('cminds_multiuseraccounts/subAccount'),
        'have_access_checkout',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'default' => '0',
            'comment' => 'Permission to have access to checkout'
        )
    );
$installer->endSetup();