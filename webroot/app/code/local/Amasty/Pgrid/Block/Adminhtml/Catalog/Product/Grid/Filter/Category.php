<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Pgrid
 */
class Amasty_Pgrid_Block_Adminhtml_Catalog_Product_Grid_Filter_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    public function getCondition()
    {

        /**
         * @var Mage_Catalog_Model_Product_Collection $collection
         */
        $collection = Mage::registry('product_collection');

        if ($collection)
        {
            if ($this->getValue())
            {
                $collection->addCategoryFilter(Mage::getModel('catalog/category')->load($this->getValue()));
            }
        
            if (0 == $this->getValue() && strlen($this->getValue()) > 0)
            {

                $collection->getSelect()->joinLeft(array('nocat_idx' => $collection->getTable('catalog/category_product')),
                    '(nocat_idx.product_id = e.entity_id)',
                    array(
                        'nocat_idx.category_id',
                    )
                );
                $collection->getSelect()->where('nocat_idx.category_id IS NULL');

            }
        }
        return null;
    }
}