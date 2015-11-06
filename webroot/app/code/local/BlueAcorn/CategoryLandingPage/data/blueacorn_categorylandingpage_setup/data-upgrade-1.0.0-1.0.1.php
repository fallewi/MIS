<?php
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$content = '<div class="category-promo-block"><a href="{{config path="web/unsecure/base_url"}}<!--replace with url key-->"> <!-- Insert Image here --> </a></div>';

$staticBlock = array(
    'title' => 'Promo Block Template',
    'identifier' => 'promo-template',
    'content' => $content,
    'is_active' => 1,
    'stores' => array(0)
);

Mage::getModel('cms/block')->setData($staticBlock)->save();