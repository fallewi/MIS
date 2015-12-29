<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

try {
    $block = Mage::getModel('cms/block');
    $block->setTitle('Legacy Orders');
    $block->setIdentifier('legacy_orders');
    $block->setStores(array(0));
    $block->setIsActive(1);
    $block->setContent('Looking for Orders Before 10/15/2014? Go Here.');
    $block->save();
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();