<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$attributeCode = 'map_required';
$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);

if ($attribute->getId() && $attribute->getFrontendInput()=='select') {
    $option['attribute_id'] = $attribute->getId();
    $option['values']       =  array('No Price No Add to Cart');
    $installer->addAttributeOption($option);
}

$installer->endSetup();