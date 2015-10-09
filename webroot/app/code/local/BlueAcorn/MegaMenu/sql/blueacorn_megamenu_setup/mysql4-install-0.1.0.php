<?php
/**
 * @package BlueAcorn_CategoryBlocks
 * @version 1.0.0
 * @author Forrest Short
 * @copyright Copyright (c) 2014 Blue Acorn, Inc.
 */
$installer = $this;
$installer->startSetup();
$attribute  = array(
    'type'          =>  'int',
    'label'         =>  'CMS Block 1',
    'input'         =>  'select',
    'source'        => 	'catalog/category_attribute_source_page',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Mega Menu"
);

$attribute2  = array(
    'type'          =>  'int',
    'label'         =>  'CMS Block 2',
    'input'         =>  'select',
    'source'        => 	'catalog/category_attribute_source_page',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Mega Menu"
);
$installer->addAttribute('catalog_category', 'cms_block_1', $attribute);
$installer->addAttribute('catalog_category', 'cms_block_2', $attribute2);
$installer->endSetup();
?>