<?php
/**
* @package     BlueAcorn\CmsStyleUpdate
* @version     1.0.1
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2014 Blue Acorn, Inc.
*/

class BlueAcorn_CmsStyleUpdate_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCustomCss(){
        $css = Mage::getBlockSingleton('cms/page')->getPage()->getCustomStyleUpdateCss();
        return $css;
    }
}
