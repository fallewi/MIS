<?php

class BlueAcorn_CategoryLandingPage_Block_Category_Navigation extends Mage_Catalog_Block_Navigation{

    public function getCategoryTree($_current)
    {
        $_helper = Mage::helper('catalog/category');
        $_baHelper = Mage::helper('blueacorn_categorylandingpage');
        $childCat = Mage::getModel('catalog/category')->getCategories($_current->getId(), 0, true, true);
        $navDepth = $_baHelper->getLayeredNavDepth();
        if (count($childCat) > 0) {

            foreach ($childCat as $_category) {
                $recurse_count = 0;

                $check_child_class = $this->check_child_par($_category, $recurse_count, $navDepth);

                echo "<dt class='" . $check_child_class . "'>";
                echo "<a href='" . $_helper->getCategoryUrl($_category) . "'>" . $_category->getName();
                echo "</a>";
                echo $this->check_child($_category, $recurse_count, $navDepth);
                echo "</dt>";

            }
        }
    }
    public function check_child($_category, &$recurse_count, $navDepth = 10){
        $recurse_count += 1;
        //ToDo: replace recursse count thing
        if ($recurse_count < $navDepth) {
            $_helper = Mage::helper('catalog/category');
            $_subsubcategories = $_category->getChildrenCategories();

            if (count($_subsubcategories) > 0) {
                echo "<dd>";
                foreach ($_subsubcategories as $_subcate) {

                    $check_child_class = $this->check_child_par($_subcate, $recurse_count);

                    if($check_child_class == "child"){
                        echo "<li class='" . $check_child_class . "'>";
                        echo "<a href='" . $_helper->getCategoryUrl($_subcate) . "'>" . $_subcate->getName();
                        echo "</a>";
                        echo $this->check_child($_subcate, $recurse_count);
                        echo "</li>";
                    } else {
                        echo "<dt class='" . $check_child_class . "-child'>";
                        echo "<a href='" . $_helper->getCategoryUrl($_subcate) . "'>" . $_subcate->getName();
                        echo "</a>";
                        echo $this->check_child($_subcate, $recurse_count);
                        echo "</dt>";
                    }

                }
                echo "</dd>";
            } else {
                return "";
            }
        }
    }

    public function check_child_par($_category, &$recurse_count, $navDepth = 10){
        $_subsubcats = $_category->getChildrenCategories();

        if (count($_subsubcats) > 0 && $recurse_count < $navDepth){
            return "parent";
        }else{
            return "child";
        }
    }

}