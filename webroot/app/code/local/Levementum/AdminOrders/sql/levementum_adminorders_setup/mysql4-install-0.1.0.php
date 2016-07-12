<?php
/**
 * @category    Levementum
 * @package     Levementum_
 * @file        ${FILE_NAME}
 * @auther      afabian@levementum.com
 * @date        10/15/13 2:33 PM
 * @brief
 * @details
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->addAttribute('order', 'admin_id', array(
    'type' => 'varchar',
    'length' => '40',
    'default' => null,
    'visible' => false,
    'required' => false,
    'nullable' => true
));

$installer->endSetup();