<?php
/**
 * @package     BlueAcorn_Productpage
 * @version
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */
$installer = $this;
$installer->startSetup();

// Ensure Owl Carousel 2 is enabled

Mage::getModel('core/config')->saveConfig('javascriptplugins/plugins/owl_two', '1');

$installer->endSetup();