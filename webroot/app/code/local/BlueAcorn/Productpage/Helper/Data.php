<?php
/**
 * @package BlueAcorn_Productpage
 * @version 1.0.0
 * @author Tyler Craft
 * @copyright Copyright (c) 2015 Blue Acorn, Inc.
 */

class BlueAcorn_Productpage_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     * This function gets the data from the admin minigrid for ships from of the product page to
     * be displayed on the frontend.
     *
     * @param $id
     * @return string
     */
    public function getShipFrom($id)
    {
        $serializedValues = Mage::getStoreConfig('productpage/general/ship_from');
        $values = unserialize($serializedValues);
        if (isset($id) || isset($serializedValues)) {
            foreach ($values as $value) {
                if ($value['ship_from'] == $id) {
                    return $value['ETA'];
                }
            }
        }
    }
}