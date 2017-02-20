<?php
/**
 * victorc@missionrs.com 01/17/2017
 * override of breadcrumbs for products
 */
class MissionRS_Breadcrumbs_Model_Observer {
    public function fullBreadcrumbCategoryPath(Varien_Event_Observer $observer) {
        $current_product    = Mage::registry('current_product');
        $current_category   = Mage::registry('current_category');

        if(!$current_category && $current_product){
            // Get Default Path
            $categories = null;
            $defaultPath = $current_product->getData('default_category_path');
            if(!empty($defaultPath)){
                $pos = strrpos($defaultPath, '/');
                $defaultCategory = $pos === false ? $defaultPath : substr($defaultPath, $pos + 1);

                $categories = $current_product->getCategoryCollection()
                    ->addAttributeToSelect('name')
                    ->addAttributeToFilter(
                        'name',
                        array( 'eq' => $defaultCategory )
                    )
                    ->setPageSize(1);

                if(!$categories){
                    $categories = $current_product->getCategoryCollection()
                        ->addAttributeToSelect('name')
                        ->setPageSize(1);
                }
            }
            else{
                $categories = $current_product->getCategoryCollection()
                    ->addAttributeToSelect('name')
                    ->setPageSize(1);
            }

            Mage::unregister('current_category');
            Mage::register('current_category', $categories->getFirstItem());
        }
    }
}