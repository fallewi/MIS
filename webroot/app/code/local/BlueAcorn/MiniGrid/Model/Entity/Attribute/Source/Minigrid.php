<?php

/**
 * Entity/Attribute/Model - attribute selection source from configuration
 *
 * This is our custom source class for parsing minigrid values into an
 * options array for dropdowns.
 *
 * This shouldn't be used directly, but instead extended and the
 * $_configNodePath variable defined.
 *
 * @category   Mage
 * @package    BlueAcorn_MiniGrid
 * @author     Chris Rasys <chris.rasys@BlueAcorn.com>
 */
abstract class BlueAcorn_MiniGrid_Model_Entity_Attribute_Source_Minigrid extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {
    protected $_configNodePath;

    /**
     * Retrieve all options for the source from configuration
     *
     * @return array
     */
    public function getAllOptions() {

        $values = unserialize(Mage::getStoreConfig($this->_configNodePath, Mage::app()->getStore()));

        $this->_options[] = '';

        if (is_array($values)) {
            foreach ($values as $value) {
                $this->_options[] = $value['title'];
            }
        }

        return $this->_options;
    }

    public function getFlatUpdateSelect($store) {
        return Mage::getResourceSingleton('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }

    public function getFlatColums() {
        $attributeDefaultValue = $this->getAttribute()->getDefaultValue();

        return array(
            $this->getAttribute()->getAttributeCode() => array(
                'type'      => $this->getAttribute()->getBackendType(),
                'unsigned'  => false,
                'is_null'   => is_null($attributeDefaultValue) || empty($attributeDefaultValue),
                'default'   => is_null($attributeDefaultValue) || empty($attributeDefaultValue) ? null : $attributeDefaultValue,
                'extra'     => null
            ));
    }

}
