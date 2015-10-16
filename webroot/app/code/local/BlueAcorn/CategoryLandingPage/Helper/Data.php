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

    public function getCategoryTree($_current)
    {
        $_helper = Mage::helper('catalog/category');
        $childCat = Mage::getModel('catalog/category')->getCategories($_current->getId());
        if (count($childCat) > 0) {

            foreach ($childCat as $_category) {
                $recurse_count = 0;

                $check_child_class = $this->check_child_par($_category->getId(),$recurse_count);
                $collaps = ($check_child_class) ? "<span class='show-cat'>+</span>" : "";
                echo "<dt class='" . $check_child_class . "'>";
                echo "<a href='" . $_helper->getCategoryUrl($_category) . "'>" . $_category->getName();
                echo "</a>" . $collaps;
                echo $this->check_child($_category->getId(), $recurse_count);
                echo "</dt>";

            }
        }
    }
    function check_child($cid, &$recurse_count){
        $recurse_count += 1;
        //ToDo: replace recursse count thing
        if ($recurse_count < 2) {
            $_helper = Mage::helper('catalog/category');
            $_subcategory = Mage::getModel('catalog/category')->load($cid);
            $_subsubcategories = $_subcategory->getChildrenCategories();

            if (count($_subsubcategories) > 0) {
                echo "<dd>";
                foreach ($_subsubcategories as $_subcate) {

                    $check_child_class = $this->check_child_par($_subcate->getId(), $recurse_count);
                    $collaps = ($check_child_class) ? "<span class='show-cat'>+</span>" : "";

                    echo "<li class='" . $check_child_class . "'>";
                    echo "<a href='" . $_helper->getCategoryUrl($_subcate) . "'>" . $_subcate->getName();
                    echo "</a>" . $collaps;
                    echo $this->check_child($_subcate->getId(), $recurse_count);
                    echo "</li>";
                }
                echo "</dd>";
            } else {
                return "";
            }
        }
    }

    function check_child_par($cid, &$recurse_count){

        $_subcat = Mage::getModel('catalog/category')->load($cid);
        $_subsubcats = $_subcat->getChildrenCategories();

        if (count($_subsubcats) > 0 && $recurse_count < 2){
            return "parent";
        }else{
            return "child";
        }
    }
}