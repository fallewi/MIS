<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


$installer = $this;

$installer->startSetup();

$fieldsSql = 'SHOW COLUMNS FROM ' . $this->getTable('eav/attribute');
$cols = $installer->getConnection()->fetchCol($fieldsSql);

if (!in_array('is_editable_on_front', $cols)) {
    $installer->run("ALTER TABLE `{$this->getTable('eav/attribute')}` ADD `is_editable_on_front` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT 0");
}

$installer->endSetup();
