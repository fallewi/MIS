<?php
/**
 * BlueAcorn_ProductVideos extension
 * 
 *
 * @category       BlueAcorn
 * @package        BlueAcorn_ProductVideos
 * @author         Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright      Copyright © 2014 Blue Acorn, Inc.
 */
/**
 * Product Video source model
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Model_Video_Source
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {
    /**
     * Get all options
     * @access public
     * @return array
     *
     */
    public function getAllOptions($withEmpty = false) {
        if (is_null($this->_options)) {
            $this->_options = Mage::getResourceModel('blueacorn_productvideos/video_collection')

                ->load()
                ->toOptionArray();
        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, array('value'=>'', 'label'=>''));
        }
        return $options;
    }

    /**
     * Get a text for option value
     * @access public
     * @param string|integer $value
     * @return string
     *
     */
    public function getOptionText($value) {
        $options = $this->getAllOptions(false);
        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    /**
     * Convert to options array
     * @access public
     * @return array
     *
     */
    public function toOptionArray() {
        return $this->getAllOptions();
    }

    /**
     * Retrieve flat column definition
     * @access public
     * @return array
     *
     */
    public function getFlatColums() {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = array(
            'unsigned'  => true,
            'default'   => null,
            'extra'     => null
        );
        if (Mage::helper('core')->useDbCompatibleMode()) {
            $column['type']     = 'int';
            $column['is_null']  = true;
        } else {
            $column['type']     = Varien_Db_Ddl_Table::TYPE_INTEGER;
            $column['nullable'] = true;
            $column['comment']  = $attributeCode . ' Product Video column';
        }
        return array($attributeCode => $column);
   }

    /**
     * Retrieve Select for update attribute value in flat table
     * @access public
     * @param   int $store
     * @return  Varien_Db_Select|null
     *
     */
    public function getFlatUpdateSelect($store) {
        return Mage::getResourceModel('eav/entity_attribute_option')
            ->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }
}