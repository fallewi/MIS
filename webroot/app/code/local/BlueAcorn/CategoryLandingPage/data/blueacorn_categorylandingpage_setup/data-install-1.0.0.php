<?php
/**
 * @package BlueAcorn_CategoryLandingPage
 * @version 0.1.0
 * @copyright Copyright (c) 2015 Blue Acorn, Inc.
 */

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'category_landing_page', array(
    'group'         => 'Display Settings',
    'input'         => 'select',
    'type'          => 'int',
    'label'         => 'Use Custom Category Landing Page',
    'visible'       => true,
    'required'      => false,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'user_defined'  => false,
    'default'       => 0,
    'source' => 'eav/entity_attribute_source_boolean'
));

$installer->endSetup();