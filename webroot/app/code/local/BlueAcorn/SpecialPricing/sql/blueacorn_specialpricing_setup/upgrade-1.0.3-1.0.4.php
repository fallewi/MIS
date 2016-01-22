<?php
$installer = Mage::getModel('catalog/resource_setup', 'core_setup');
$installer->startSetup();

// Gather current product data
$productCollection = Mage::getModel('catalog/product')->getCollection()
    ->addAttributeToSelect('map_required');
$mapKey = array(
    'Disabled' => BlueAcorn_SpecialPricing_Model_Entity_Attribute_Source_Map::DISABLED,
    'Requires MAP Email' => BlueAcorn_SpecialPricing_Model_Entity_Attribute_Source_Map::EMAIL,
    'Requires MAP Call' => BlueAcorn_SpecialPricing_Model_Entity_Attribute_Source_Map::CALL
);
$prodKey = array();
foreach($productCollection as $product) {
    $prodKey[$product->getId()] = $mapKey[$product->getAttributeText('map_required') ?: 'Disabled'];
}

// Update attribute source model
$installer->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'map_required',
    'source_model',
    'blueacorn_specialpricing/entity_attribute_source_map'
);

// Update product attribute values
foreach($productCollection as $product) {
    $product->setMapRequired($prodKey[$product->getId()]);
    $product->getResource()->saveAttribute($product, 'map_required');
}

$installer->endSetup();
