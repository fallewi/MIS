<?php
/**
 * @package BlueAcorn_CategoryBlocks
 * @version 0.2.0
 * @copyright Copyright (c) 2014 Blue Acorn, Inc.
 */
$installer = $this;
$installer->startSetup();
$installer->removeAttribute('catalog_category', 'cms_block_1');
$installer->endSetup();