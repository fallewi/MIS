<?php

/**
 * @package BlueAcorn_Productpage
 * @version 1.0.0
 * @author Ryan Corn
 * @copyright Copyright (c) 2015 Blue Acorn, Inc.
 */

class BlueAcorn_Productpage_Model_System_Config_Source_ShipFrom extends BlueAcorn_MiniGrid_Model_System_Config_Source_Minigrid_Abstract {

    /**
     * @return array
     */
    protected function _getShipFrom() {
        $attribute = Mage::getModel("eav/entity_attribute")->loadByCode("catalog_product", "ships_from");
        $values = $attribute->getSource()->getAllOptions(true, true);
        if (!is_array($values)) {
            $values = array();
        }

        $valueMap = array();
        foreach ($values as $valuePair) {
            $label = isset($valuePair['label']) ? $valuePair['label'] : null;
            $value = isset($valuePair['value']) ? $valuePair['value'] : null;
            if ($label && $value) {
                $valueMap[$value] = $label;
            }
        }
        asort($valueMap);
        return $valueMap;
    }


    /**
     * @return array
     */
    protected function _getFields() {
        return array(
            "ship_from" => array("width" => "600px", "type" => "select", "options" => $this->_getShipFrom()),
            "ETA" => array("width" => "600px", "type" => "textarea"),
        );
    }
}