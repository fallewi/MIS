<?php
$installer=$this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn(
        $installer->getTable('cminds_multiuseraccounts/subAccount'),
        'restricted_categories',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'length'    => 255,
            'comment' => 'Restricted Categories'
        )
    );

$table = $installer->getConnection()
    ->newTable($installer->getTable('cminds_multiuseraccounts/transfer'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn('subaccount_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Sub Account Id')
    ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Quote ID')
    ->addColumn('creator_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Quote ID')
    ->addColumn('was_transfered', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Quote ID');
$installer->getConnection()->createTable($table);

$installer->endSetup();
