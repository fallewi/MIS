<?php
/**
 * @package     BlueAcorn
 * @subpackage  CategoryLandingPage
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */
class BlueAcorn_CategoryLandingPage_Block_Category_View extends Mage_Catalog_Block_Category_View {
    /**
     * Gets category collection of children categories of the current category
     *
     * @return array
     */
    public function getChildCategories() {
        $subCats = array();
        $category = Mage::registry('current_category');
        $parentId = $category->getId();
        $children = Mage::getModel('catalog/category')->getCategories($parentId, 0, true, true)
            ->addAttributeToSelect(array('thumbnail', 'image', 'url'))
            ->addAttributeToFilter('is_active', 1);
        // Counter accounts for data imports causing all categories to have a position of 0
        $nextPosition = 1000;
        foreach ($children as $cat) {
            if($cat->getLevel() == ($category->getLevel() +1)) {
                if($cat->getPosition() !== '0') {
                    $subCats[$cat->getPosition()] = $cat;
                } else {
                    $subCats[$nextPosition] = $cat;
                    $nextPosition++;
                }
            }
        }
        ksort($subCats);
        return $subCats;
    }
    /**
     * Returns the media category thumbnail image URL
     *
     * @param $category
     * @return string
     */
    public function getThumbnailImageUrl($category) {
        $helper = Mage::helper('blueacorn_categorylandingpage');
        if($image = $category->getThumbnail()) {
            $url = Mage::getBaseUrl('media') . 'catalog/category/' . $image;
        }else{
            $url = $helper->getDefaultCategoryThumbnail();
        }
        return $url;
    }
}

