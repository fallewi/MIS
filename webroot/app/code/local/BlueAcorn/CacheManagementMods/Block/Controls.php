<?php
/**
 * @package     BlueAcorn\CacheManagementMods
 * @version
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */
class BlueAcorn_CacheManagementMods_Block_Controls extends Mage_Adminhtml_Block_Template {
    public function getButtonUrl($action = null) {
        return $this->getUrl( '*/cachemod/index', array('action'=>$action));
    }
}
