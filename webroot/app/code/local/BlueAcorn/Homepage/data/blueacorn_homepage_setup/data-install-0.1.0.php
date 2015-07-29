<?php
/**
 * @package BlueAcorn_Homepage
 * @version 0.2.0
 * @author Tyler Craft
 * @copyright Copyright (c) 2015 Blue Acorn, Inc.
 */
$pageId = $collection = Mage::getModel('cms/page')->getCollection()->addFieldToFilter('identifier', 'home')->getFirstItem()->getId();
$page = Mage::getModel('cms/page')->load($pageId);

$cmsPage = Array (
    'page_id' => $pageId,
    'title' => 'Home page',
    'root_template' => 'one_column',
    'identifier' => 'home',
    'content' => "<span></span>",
    'is_active' => 1,
    'stores' => array(1),
    'sort_order' => 0
);

$page->setData($cmsPage)->save();
