<?php
/**
 * @package BlueAcorn_Homepage
 * @version 1.0.0
 * @author Tyler Craft
 * @copyright Copyright (c) 2015 Blue Acorn, Inc.
 */

class BlueAcorn_Homepage_Helper_Data extends Mage_Core_Helper_Abstract {
	
	public function getHeroBlock() {
        $blockId = Mage::getStoreConfig('homepage/homepage_hero/homepage_hero_cms_block');
        $cmsBlock = Mage::getModel('cms/block')->load($blockId);
        return $cmsBlock->getIdentifier();
    }
}