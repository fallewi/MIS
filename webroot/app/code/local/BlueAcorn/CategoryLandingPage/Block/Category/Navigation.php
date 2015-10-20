<?php

class BlueAcorn_CategoryLandingPage_Block_Category_Navigation extends Mage_Catalog_Block_Navigation{

    /**
     * Echos out the Category tree for the Nav on Category Landing Page
     *
     * @param $_current
     * @param $childCat
     */
    public function getCategoryTree($childCat)
    {
        $_helper = Mage::helper('catalog/category');
        $_baHelper = Mage::helper('blueacorn_categorylandingpage');
        $navDepth = $_baHelper->getLayeredNavDepth();

        // Check to see if the current category has child categories
        if (count($childCat) > 0) {
            foreach ($childCat as $_category) {
                $recursiveCount = 0;
                $htmlClass = $this->checkIfChild($_category);

                echo "<dt class='" . $htmlClass . "'>";
                echo "<a href='" . $_helper->getCategoryUrl($_category) . "'>" . $_category->getName();
                echo "</a>";
                echo $this->getSubCategoryTree($_category, $recursiveCount, $navDepth);
                echo "</dt>";
            }
        }
    }

    /**
     * Recursive function that echos out the subcategories for the navigation on category landing page
     *
     * @param $_category
     * @param $recursiveCount
     * @param int $navDepth
     * @param int $categoryLvl
     * @return string
     */
    public function getSubCategoryTree($_category, &$recursiveCount, $navDepth = 10, $categoryLvl = 0){
        $recursiveCount += 1;

        // If recursive count is less than or equal the nav depth set in admin
        // or the category level is not equal to the nav depth. (This is for subcategories with subcategories)
        if ($recursiveCount <= $navDepth || $categoryLvl <= $navDepth + 1) {
            $_helper = Mage::helper('catalog/category');
            $_subsubcategories = $_category->getChildrenCategories();

            if (count($_subsubcategories) > 0) {
                echo "<dd>";

                foreach ($_subsubcategories as $_subcate) {
                    $htmlClass = $this->checkIfChild($_subcate);

                    // if category is has not child, else the subcategories have children
                    if($htmlClass == "child"){
                        echo "<li class='" . $htmlClass . "'>";
                        echo "<a href='" . $_helper->getCategoryUrl($_subcate) . "'>" . $_subcate->getName();
                        echo "</a>";
                        echo $this->getSubCategoryTree($_subcate, $recursiveCount, $navDepth);
                        echo "</li>";
                    } else {
                        echo "<ul class='" . $htmlClass . "-child'>";
                        echo "<a href='" . $_helper->getCategoryUrl($_subcate) . "'>" . $_subcate->getName();
                        echo "</a>";
                        echo $this->getSubCategoryTree($_subcate, $recursiveCount, $navDepth, $_subcate->getLevel());
                        echo "</ul>";
                    }
                }
                echo "</dd>";
            } else {
                return "";
            }
        }
    }

    /**
     * Function to check if a category has children
     *
     * @param $_category
     * @return string
     */
    public function checkIfChild($_category){
        $_subsubcats = $_category->getChildrenCategories();

        if (count($_subsubcats) > 0){
            return "parent";
        }else{
            return "child";
        }
    }
}