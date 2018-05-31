<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class Amasty_Orderattr_DropdownController extends  Mage_Core_Controller_Front_Action
{
    public function getChildDataAction()
    {
        $parentId = Mage::app()->getRequest()->getParam('parentId');
        $childField = Mage::app()->getRequest()->getParam('childField');
        $parentAttributeId = Mage::app()->getRequest()->getParam('parentAttributeId');
        if (!($parentId && $childField))
        {
            $this->getResponse()->setBody('');
        } else 
        {
            $collection = Mage::getModel('eav/entity_attribute')->getCollection();
            $collection ->addFieldToFilter('is_visible_on_front', 1)
                        ->addFieldToFilter('parent_dropdown', $parentAttributeId)
                        ->getSelect()->order('sorting_order');
            $attributes = $collection->load();
            foreach ($attributes as $attribute) {
                if($attribute->getAttributeCode() == $childField) {
                    $options = $this->_getAllOptions($attribute, $parentId, true, true);
                }
            }
            $attributeValue = Mage::helper('amorderattr')->getAttributeValue($attribute);
            $response = array($options, $attributeValue);
            $result = Zend_Json::encode($response);
            $this->getResponse()->setBody(
                $result
            );
        }
    }
    
    protected function _getAllOptions($attribute, $parentId, $withEmpty = true, $defaultValues = false)
    {
        $storeId = Mage::app()->getStore()->getId();
        $resuorce = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getId())
            ->addFieldToFilter('parent_option_id', $parentId)
            ->setStoreFilter($storeId);
        $resuorce->getSelect()->order('sort_order');
        $collection = $resuorce->load();
        
        $options[$storeId] = $collection->toOptionArray();
        $optionsDefault[$storeId] = $collection->toOptionArray('default_value');
        
        $result = ($defaultValues ? $optionsDefault[$storeId] : $options[$storeId]);
        if ($withEmpty) {
            array_unshift($result, array('label' => '', 'value' => ''));
        }
        
        return $result;
    }
}