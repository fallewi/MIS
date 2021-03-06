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

    protected function _prepareLayout() {
        parent::_prepareLayout();

        $_current = Mage::registry('current_category');
        if($_current->getCategoryLandingPage()) {
            if($root = $this->getLayout()->getBlock('root')) {
                $root->addBodyClass('category-landing-page');
            }
        }

        return $this;
    }
}

