<?php
/**
 * @package     BlueAcorn\EasyAttributes
 * @version     
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */ 
class BlueAcorn_EasyAttributes_Model_Resource_Catalog_Config extends Mage_Catalog_Model_Resource_Config {
    /**
     * Retrieve Product Attributes Used in Catalog Product listing
     *
     * @return array
     */
    public function getAttributesUsedInListing()
    {
        $attributes = Mage::getConfig()->getNode('frontend/product/collection/custom_attributes');
        if ($attributes) {
            $attributes = $attributes->asArray();
            $attributes = is_array($attributes) ? array_keys($attributes) : false;
        }

        $adapter = $this->_getReadAdapter();
        $storeLabelExpr = $adapter->getCheckSql('al.value IS NOT NULL', 'al.value', 'main_table.frontend_label');

        $select  = $adapter->select()
            ->from(array('main_table' => $this->getTable('eav/attribute')))
            ->join(
                array('additional_table' => $this->getTable('catalog/eav_attribute')),
                'main_table.attribute_id = additional_table.attribute_id'
            )
            ->joinLeft(
                array('al' => $this->getTable('eav/attribute_label')),
                'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int)$this->getStoreId(),
                array('store_label' => $storeLabelExpr)
            )
            ->where('main_table.entity_type_id = ?', (int)$this->getEntityTypeId())
            ->where('additional_table.used_in_product_listing = 1 OR main_table.attribute_code in (?)', $attributes);

        return $adapter->fetchAll($select);
    }
}