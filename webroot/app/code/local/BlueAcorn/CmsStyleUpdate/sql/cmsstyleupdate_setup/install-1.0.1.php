<?php
/**
* @package     BlueAcorn\CmsStyleUpdate
* @version     1.0.1
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2014 Blue Acorn, Inc.
*/

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$cms_table = $installer->getTable('cms_page');
$enterprise_cms_revision_table = $installer->getTable('enterprise_cms_page_revision');
$conn->addColumn($cms_table, 'custom_style_update_css', 'text');
$conn->addColumn($enterprise_cms_revision_table, 'custom_style_update_css', 'text');

$installer->endSetup();

/**
Rollback for mysql:

ALTER TABLE `cms_page` DROP `custom_style_update_css`;
ALTER TABLE `enterprise_cms_page_revision` DROP `custom_style_update_css`;

 */