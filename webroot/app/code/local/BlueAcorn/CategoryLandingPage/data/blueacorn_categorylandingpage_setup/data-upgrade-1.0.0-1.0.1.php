<?php
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$content = '<a href="{{config path="web/unsecure/base_url"}}<!--replace with url key-->"> <!-- Insert Image here --> </a>';

$staticBlock = array(
    'title' => 'Promo Block Template',
    'identifier' => 'promo-template',
    'content' => $content,
    'is_active' => 1,
    'stores' => array(0)
);

Mage::getModel('cms/block')->setData($staticBlock)->save();