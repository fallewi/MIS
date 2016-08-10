<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */

$installer = $this;

$installer->startSetup();

$fieldsSql = 'SHOW COLUMNS FROM ' . $this->getTable('eav/attribute');
$cols = $installer->getConnection()->fetchCol($fieldsSql);

if (!in_array('order_grid_after', $cols)) {
    $installer->run("ALTER TABLE `{$this->getTable('eav/attribute')}` ADD `order_grid_after` VARCHAR(255) DEFAULT NULL");
}

if (!in_array('file_size', $cols)) {
    $installer->run("ALTER TABLE `{$this->getTable('eav/attribute')}` ADD `file_size` SMALLINT( 5 ) UNSIGNED NOT NULL ;");
    $installer->run("ALTER TABLE `{$this->getTable('eav/attribute')}` ADD `file_types` VARCHAR( 255 ) NOT NULL ;");
}

$installer->endSetup();
