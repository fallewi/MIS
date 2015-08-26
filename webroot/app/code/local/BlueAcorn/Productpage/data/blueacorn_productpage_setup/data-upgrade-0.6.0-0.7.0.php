<?php
/**
 * @package BlueAcorn_Productpage
 * @version 0.2.0
 * @copyright Copyright (c) 2015 Blue Acorn, Inc.
 */

$installer = $this;
$setup = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'features_benefits', array(
    'attribute_set' => 'Default',
    'group' => 'Details',
    'type' => 'text',
    'backend' => '',
    'frontend' => '',
    'label' => 'Features & Benefits',
    'input' => 'textarea',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'used_in_product_listing' => false,
    'unique' => false,
    'visible_on_front' => false,
    'is_html_allowed_on_front' => true
));

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'resource_badges', array(
    'attribute_set' => 'Default',
    'group' => 'Details',
    'type' => 'text',
    'backend' => '',
    'frontend' => '',
    'label' => 'Resource Badges',
    'input' => 'textarea',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'used_in_product_listing' => false,
    'unique' => false,
    'visible_on_front' => false,
    'is_html_allowed_on_front' => true
));

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'resource_downloads', array(
    'attribute_set' => 'Default',
    'group' => 'Details',
    'type' => 'text',
    'backend' => '',
    'frontend' => '',
    'label' => 'Resource Downloads',
    'input' => 'textarea',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'used_in_product_listing' => false,
    'unique' => false,
    'visible_on_front' => false,
    'is_html_allowed_on_front' => true
));

$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'ships_from', array(
    'attribute_set' => 'Default',
    'group' => 'Details',
    'type' => 'text',
    'backend' => '',
    'frontend' => '',
    'label' => 'Ships From',
    'input' => 'textarea',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'used_in_product_listing' => false,
    'unique' => false,
    'visible_on_front' => false,
    'is_html_allowed_on_front' => true
));

$installer->endSetup();
