<?php
/**
 * @package     BlueAcorn\CategoryLandingPage
 * @version
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */
class BlueAcorn_CategoryLandingPage_Helper_Data extends Mage_Core_Helper_Abstract {
    public $childCategories = null;

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

    /**
     * Returns if layered nav for category page has been enabled in the system config
     *
     * @return boolean
     */
    public function getLayeredNavEnabled() {
        $configValue = Mage::getStoreConfig('categorylandingpage/general/layered_nav_enable', Mage::app()->getStore());
        return $configValue;
    }

    /**
     * Returns the Layered Nav depth set in the system config
     *
     * @return string
     */
    public function getLayeredNavDepth() {
        $configValue = Mage::getStoreConfig('categorylandingpage/general/layered_nav_depth', Mage::app()->getStore());
        return $configValue;
    }

    /**
     * Returns a collection of the subcategories for the current category
     *
     * @param $_current
     * @return collection
     */
    public function getSubcategories($_current){

        if ($this->childCategories === null) {
            $currentId = $_current->getId();
            $this->childCategories = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect(array('thumbnail', 'name', 'url_key'))
                ->addAttributeToFilter('parent_id', $currentId);
        }
        return $this->childCategories;
    }
}