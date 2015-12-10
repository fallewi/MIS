<?php
/**
 * @package     BlueAcorn\CustomerGroupCustomizations
 * @version     0.1.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('catalog_category', 'use_in_customer_groups',  array(
    'type'     => 'int',
    'label'    => 'Allow in Customer Groups',
    'input'    => 'select',
    'source'   => 'eav/entity_attribute_source_boolean',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => 0
));

$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'use_in_customer_groups',
    '11'
);

$table = $installer->getConnection()
    ->addColumn($installer->getTable('customer/customer_group'), 'linked_category', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length' => 11,
        'nullable'  => false,
        'default' => -1,
        'comment' => 'Linked Category' )
    );

if (method_exists($this->_conn, 'resetDdlCache')) {
    $this->_conn->resetDdlCache();
}

$installer->endSetup();