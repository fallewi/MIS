<?php

/**
 * @package     BlueAcorn\SpecialPricing
 * @version     1.0.4
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */

/** @var BlueAcorn_SpecialPricing_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

// If msrp_true attribute exists, it was loaded from old site database. In this case, it should be removed.
$attributeCode = 'msrp_true';
$entityTypeId = $installer->getEntityTypeId(Mage_Catalog_Model_Product::ENTITY);
if ($installer->getAttributeId($entityTypeId, $attributeCode)) {
    $installer->removeAttribute($entityTypeId, $attributeCode);
}

$installer->endSetup();