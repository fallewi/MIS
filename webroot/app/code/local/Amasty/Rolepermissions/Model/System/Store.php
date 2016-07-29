<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Model_System_Store extends Mage_Adminhtml_Model_System_Store
{
    /**
     * Get websites as id => name associative array
     *
     * @param bool $withDefault
     * @param string $attribute
     * @return array
     */
    public function getWebsiteOptionHash($withDefault = false, $attribute = 'name')
    {
        $options = parent::getWebsiteOptionHash($withDefault, $attribute);

        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($rule->getScopeStoreviews())
        {
            $accessible = $rule->getPartiallyAccessibleWebsites();

            if (isset($options[0]))
                unset($options[0]); // Unset admin store

            foreach ($options as $id => $value)
            {
                if (!in_array($id, $accessible))
                    unset($options[$id]);
            }
        }

        return $options;
    }

    /**
     * Get store views as id => name associative array
     *
     * @param bool $withDefault
     * @param string $attribute
     * @return array
     */
    public function getStoreOptionHash($withDefault = false, $attribute = 'name')
    {
        $options = parent::getStoreOptionHash($withDefault = false, $attribute);

        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($scopeStoreViews = $rule->getScopeStoreviews())
        {
            $options = array_intersect_key($options, array_flip($scopeStoreViews));
        }

        return $options;
    }

    /**
     * Get store groups as id => name associative array
     *
     * @param string $attribute
     * @return array
     */
    public function getStoreGroupOptionHash($attribute = 'name')
    {
        $options = parent::getStoreGroupOptionHash($attribute);

        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($scopeStoreViews = $rule->getScopeStoreviews())
        {
            foreach ($scopeStoreViews as $key => $scopeStoreView) {
                $storeView = Mage::getModel('core/store')->load($scopeStoreView);
                $groupId = $storeView->getGroupId();
                $restrictedOptions[$groupId] = $options[$groupId];
            }
            $options = $restrictedOptions;
        }

        return $options;
    }
}
