<?php

/**
 * Levementum_Customer setup script
 *
 * @category   Levementum
 * @package    Levementum_Customer
 * @author 	   victorc@missionrs.com
 */

$installer = $this;
$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('customer');
$idAttributeOldSelect = $installer->getAttribute($entityTypeId,'assigned_salesperson', 'attribute_id');

$installer->updateAttribute($entityTypeId, $idAttributeOldSelect, array(
    'type'                         => 'varchar',
    'length'                       => 40
));

$installer->run("DELETE FROM `customer_entity_int` where `attribute_id` = {$idAttributeOldSelect}");

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'assigned_salesperson');
$attribute->setData('used_in_forms' , array(
    'adminhtml_customer','customer_account_edit', 'adminhtml_checkout'
));

$attribute->save();

$installer->endSetup();
