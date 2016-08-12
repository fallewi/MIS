<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_List
 */ 
class Amasty_List_Block_Items extends Mage_Catalog_Block_Product_Abstract
{ 
    protected $_allLists = null;
    
    //for "move item to"  function 
    public function getAllLists($exclude=0)
    {
        if (is_null($this->_allLists)){
            $this->_allLists = Mage::getResourceModel('amlist/list_collection')
                ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
                ->addFieldToFilter('list_id', array('neq' => $exclude))
                ->load(); 
        }
        return $this->_allLists;
    }
    
    public function getList()
    {
        return Mage::registry('current_list');
    }

    public function getProductItemUrl($item){
        $description = $item->getDescr();
        if( $description != '' && strpos($description, 'grouped') == 0 ){
            $groupedId = explode(':', $description);
            if(array_key_exists(1, $groupedId)){
                $groupedId = trim($groupedId[1]);
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($groupedId);
                $item->setGroupedParentProduct($product);
                return $product->getProductUrl();
            }
        }

        return  $item->getProduct()->getProductUrl();
    }

    public function getProductItemImage($item){
        $product = $item->getProduct();

        $url = $this->helper('catalog/image')->init($product, 'small_image')->resize(113, 113);
        /* if no image and parent exist*/
        if($item->getGroupedParentProduct() && ($url == '' || strpos($url, 'placeholder')) ){
            $product = $item->getGroupedParentProduct();
            $url = $this->helper('catalog/image')->init($product, 'small_image')->resize(113, 113);
        }

        return $url;
    }

}