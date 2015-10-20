<?php

class BlueAcorn_CategoryLandingPage_Block_Category_Navigation extends Mage_Catalog_Block_Navigation{

    public function getCategoryTree($_current)
    {
        $_helper = Mage::helper('catalog/category');
        $_baHelper = Mage::helper('blueacorn_categorylandingpage');
        $childCat = Mage::getModel('catalog/category')->getCategories($_current->getId(), 0, true, true);
        $navDepth = $_baHelper->getLayeredNavDepth();

        // Check to see if the current category has child categories
        if (count($childCat) > 0) {
            foreach ($childCat as $_category) {
                $recursiveCount = 0;
                $htmlClass = $this->checkIfChild($_category);

                echo "<dt class='" . $htmlClass . "'>";
                echo "<a href='" . $_helper->getCategoryUrl($_category) . "'>" . $_category->getName();
                echo "</a>";
                echo $this->getSubCategories($_category, $recursiveCount, $navDepth);
                echo "</dt>";
            }
        }
    }
    public function getSubCategories($_category, &$recursiveCount, $navDepth = 10, $categoryLvl = 0){
        $recursiveCount += 1;

        if ($recursiveCount <= $navDepth || $categoryLvl <= $navDepth + 1) {
            $_helper = Mage::helper('catalog/category');
            $_subsubcategories = $_category->getChildrenCategories();

            if (count($_subsubcategories) > 0) {
                echo "<dd>";

                foreach ($_subsubcategories as $_subcate) {
                    $htmlClass = $this->checkIfChild($_subcate);

                    if($htmlClass == "child"){
                        echo "<li class='" . $htmlClass . "'>";
                        echo "<a href='" . $_helper->getCategoryUrl($_subcate) . "'>" . $_subcate->getName();
                        echo "</a>";
                        echo $this->getSubCategories($_subcate, $recursiveCount, $navDepth);
                        echo "</li>";
                    } else {
                        echo "<ul class='" . $htmlClass . "-child'>";
                        echo "<a href='" . $_helper->getCategoryUrl($_subcate) . "'>" . $_subcate->getName();
                        echo "</a>";
                        echo $this->getSubCategories($_subcate, $recursiveCount, $navDepth, $_subcate->getLevel());
                        echo "</ul>";
                    }
                }
                echo "</dd>";
            } else {
                return "";
            }
        }
    }

    public function checkIfChild($_category){
        $_subsubcats = $_category->getChildrenCategories();

        if (count($_subsubcats) > 0){
            return "parent";
        }else{
            return "child";
        }
    }
}