<?php

class BlueAcorn_CategoryLandingPage_Block_Category_Navigation extends Mage_Catalog_Block_Navigation{

    /**
     * Echos out the Category tree for the Nav on Category Landing Page
     *
     *
     * @param $childCat
     */
    public function getCategoryTree($childCat)
    {
        $_helper = Mage::helper('catalog/category');
        $_baHelper = Mage::helper('blueacorn_categorylandingpage');
        $navDepth = $_baHelper->getLayeredNavDepth();
        if($navDepth === ""){
            $navDepth = 10;
        }
        // Check to see if the current category has child categories
        if (count($childCat) > 0) {
            foreach ($childCat as $_category) {
                $recursiveCount = 0;
                $htmlClass = $this->checkIfChild($_category, $_category->getLevel(), $navDepth);

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
    public function getSubCategoryTree($_category, &$recursiveCount, $navDepth, $categoryLvl = 0){
        $recursiveCount += 1;

        // If recursive count is less than or equal the nav depth set in admin
        // or the category level is not equal to the nav depth. (This is for subcategories with subcategories)
        if ($recursiveCount <= $navDepth || $categoryLvl <= $navDepth + 1) {
            $_helper = Mage::helper('catalog/category');
            $_subsubcategories = $_category->getChildrenCategories();

            if (count($_subsubcategories) > 0) {
                echo '<dd><ol>';

                foreach ($_subsubcategories as $_subcate) {
                    $htmlClass = $this->checkIfChild($_subcate, $_subcate->getLevel(), $navDepth);

                    // if category is has not child, else the subcategories have children
                    if($htmlClass == "child"){
                        echo "<li class='" . $htmlClass . "'>";
                        echo "<a href='" . $_helper->getCategoryUrl($_subcate) . "'>" . $_subcate->getName();
                        echo "</a>";
                        echo $this->getSubCategoryTree($_subcate, $recursiveCount, $navDepth);
                        echo "</li>";
                    } else {
                        echo "<ol class='" . $htmlClass . "-child'>";
                        echo "<a href='" . $_helper->getCategoryUrl($_subcate) . "'>" . $_subcate->getName();
                        echo "</a>";
                        echo $this->getSubCategoryTree($_subcate, $recursiveCount, $navDepth, $_subcate->getLevel());
                        echo "</ol>";
                    }
                }
                echo "</ol></dd>";
            } else {
                return "";
            }
        }
    }

    /**
     * Function to check if a category has children
     *
     * @param $_category
     * @param $categoryLvl
     * @param $navDepth
     * @return string
     */
    public function checkIfChild($_category, $categoryLvl, $navDepth){
        $_subsubcats = $_category->getChildrenCategories();

        if (count($_subsubcats) > 0 && $categoryLvl <= $navDepth + 1){
            return "parent";
        }else{
            return "child";
        }
    }
}