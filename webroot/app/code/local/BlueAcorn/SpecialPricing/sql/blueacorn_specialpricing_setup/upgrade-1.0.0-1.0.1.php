<?php
$setup = new Mage_Catalog_Model_Resource_Setup('core_setup');

$attr = array (
    'attribute_model' => NULL,
    'backend' => NULL,
    'type' => 'int',
    'table' => NULL,
    'frontend' => NULL,
    'input' => 'select',
    'label' => 'MAP Required',
    'frontend_class' => NULL,
    'source' => 'eav/entity_attribute_source_table',
    'required' => '0',
    'user_defined' => '1',
    'default' => '12',
    'unique' => '0',
    'note' => NULL,
    'input_renderer' => NULL,
    'global' => '1',
    'visible' => '1',
    'searchable' => '0',
    'filterable' => '0',
    'comparable' => '0',
    'visible_on_front' => '0',
    'is_html_allowed_on_front' => '1',
    'is_used_for_price_rules' => '0',
    'filterable_in_search' => '0',
    'used_in_product_listing' => '0',
    'used_for_sort_by' => '0',
    'is_configurable' => '0',
    'apply_to' => NULL,
    'visible_in_advanced_search' => '0',
    'position' => '0',
    'wysiwyg_enabled' => '0',
    'used_for_promo_rules' => '0',
    'search_weight' => '1',
    'option' =>
        array (
            'values' =>
                array (
                    0 => 'Disabled',
                    1 => 'Requires MAP Call',
                    2 => 'Requires MAP Email',
                ),
        ),
);
$setup->removeAttribute('catalog_product', 'map_required');

$setup->addAttribute('catalog_product', 'map_required', $attr);

$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'map_required');
$attribute->setStoreLabels(array (
));
$attribute->save();