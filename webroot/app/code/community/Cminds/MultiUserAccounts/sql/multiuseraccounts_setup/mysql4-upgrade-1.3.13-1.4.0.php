<?php
$installer=$this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('cminds_multiuseraccounts/subAccount'),
        'assigned_approvers',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'length'    => 255,
            'comment' => 'Assigned Approvers'
        )
    );
$installer->endSetup();