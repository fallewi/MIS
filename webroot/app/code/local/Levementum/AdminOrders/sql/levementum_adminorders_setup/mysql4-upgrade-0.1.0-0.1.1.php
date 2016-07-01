<?php
/**
 * @category    Levementum
 * @package     Levementum_
 * @file
 * @author      victorc@missionrs.com
 * @date        06/21/2016
 * @brief
 * @details
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->updateAttribute('order', 'admin_id', array(
    'type' => 'varchar',
    'length' => '40',
    'default' => null,
    'visible' => false,
    'required' => false,
    'nullable' => true
));

$installer->endSetup();