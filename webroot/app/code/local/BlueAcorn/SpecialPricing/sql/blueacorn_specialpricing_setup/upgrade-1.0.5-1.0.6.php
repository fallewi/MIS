<?php
	$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
	$installer->startSetup();
	
	$attributeCode = 'map_required';
	$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
	
	if ( $attribute->getId() && $attribute->getFrontendInput() == 'select' ) {
		$option['values'] = array('No Price Guest User');
		$option['attribute_id'] = $attribute->getId();
		$installer->addAttributeOption($option);
	}
	
	$installer->endSetup();