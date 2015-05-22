<?php
/**
 * Copyright (c) 2013 S.L.I. Systems, Inc. (www.sli-systems.com) - All Rights Reserved
 * This file is part of Learning Search Connect.
 * Learning Search Connect is distribute under license,
 * go to www.sli-systems.com/LSC for full license details.
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 * 
 * Install script for version 1.0.0
 * Sets defaults into system config data table
 *
 * @package SLI
 * @subpackage Search
 */

// This function will be used to sort the attributes array
function cmp($attr1, $attr2) {
    return strcmp($attr1['attribute'], $attr2['attribute']) > 0 ? 1 : -1;
}

//Save out the default attributes to the core config data table
$defaultAttributes = Mage::getConfig()->getNode('default/sli_search/default_attributes')->asArray();

$productEntityType = Mage::getModel('eav/entity_type')->loadByCode('catalog_product');
$attributeCollection = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($productEntityType->getId());

$attributes = array();
foreach($attributeCollection as $attribute) {
    $code = $attribute->getAttributeCode();
    if (isset($defaultAttributes[$code])) {
        $attributes[]['attribute'] = $attribute->getAttributeCode();
        unset($defaultAttributes[$code]);
    }
}

// The attributes left in the array are non-eav
foreach ($defaultAttributes as $attributeCode => $val) {
    $attributes[]['attribute'] = $attributeCode;
}

usort($attributes, "cmp");

Mage::getModel('core/config_data')
    ->setPath('sli_search/attributes/attributes')
    ->setScope('default')
    ->setScopeId(0)
    ->setValue(serialize($attributes))
    ->save();


//Save out the cron job to the core config data table
$frequency = Mage::getConfig()->getNode('default/sli_search/cron/frequency');
$time = explode(",", Mage::getConfig()->getNode('default/sli_search/cron/time'));

$cronTab = Mage::helper('sli_search')->getCronTimeAsCrontab($frequency, $time);

Mage::getModel("sli_search/system_config_backend_cron")->saveCronTab($cronTab);