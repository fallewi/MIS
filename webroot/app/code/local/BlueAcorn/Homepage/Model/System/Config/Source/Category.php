<?php
/**
 * @package BlueAcorn_Homepage
 * @version 0.2.0
 * @author Tyler Craft
 * @copyright Copyright (c) 2015 Blue Acorn, Inc.
 */
class BlueAcorn_Homepage_Model_System_Config_Source_Category {

    public function toOptionArray() {
        $categories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('id')
            ->addAttributeToSelect('name');

        $option_array = array();
        $option_array[] = array('value' => 0, 'label' => Mage::helper('blueacorn_homepage')->__('None'));
        foreach ($categories as $category) {
                $option_array[] = array('value' => $category->getId(), 'label' => Mage::helper('blueacorn_homepage')->__($category->getName()));
        }
        return $option_array;
    }
}