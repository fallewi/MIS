<?php
/**
 * @package     BlueAcorn\CategoryLandingPage
 * @version
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */
class BlueAcorn_CategoryLandingPage_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     * Returns whether or not the category has child categories.
     *
     * @param $category
     * @return bool
     */
    public function isTopCategory($category) {
        if(!is_null($category)) {
            return ($category->getChildren() !== "") ? true : false;
        } else {
            return false;
        }
    }
    /**
     * Returns the URL to the default category thumbnail set in the admin.
     *
     * @return string
     */
    public function getDefaultCategoryThumbnail() {
        $mediaUrl = Mage::getBaseUrl('media');
        return $mediaUrl . "catalog/product/placeholder/" . Mage::getStoreConfig('catalog/placeholder/small_image_placeholder');
    }
}