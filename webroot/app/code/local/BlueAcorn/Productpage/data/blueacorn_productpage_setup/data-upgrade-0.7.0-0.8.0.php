<?php
/**
 * @package     BlueAcorn\Productpage
 * @version
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */

$installer = $this;
$setup = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$setup->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'resource_badges',
    'frontend_label',
    'Certification Badges'
);

$installer->endSetup();