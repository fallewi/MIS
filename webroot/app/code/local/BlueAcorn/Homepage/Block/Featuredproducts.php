<?php
/**
* @package BlueAcorn_CategoryBlocks
* @version 1.0.0
* @author Tyler Craft
* @copyright Copyright (c) 2014 Blue Acorn, Inc.
*/

class BlueAcorn_Homepage_Block_Featuredproducts extends Mage_Catalog_Block_Product_Abstract {

    protected $featured_category;

    protected function _construct()
    {
        $this->featured_category = Mage::getStoreConfig('homepage/featured_products/featured_category');
        $this->featured_promo = Mage::getStoreConfig('homepage/featured_products/featured_promo');
        $this->featured_promo_title = Mage::getStoreConfig('homepage/featured_products/featured_promo_title');
        $this->featured_promo_short_desc = Mage::getStoreConfig('homepage/featured_products/featured_promo_short_desc');
        $this->featured_promo_btn_text = Mage::getStoreConfig('homepage/featured_products/featured_promo_btn_text');
        $this->featured_promo_btn_link = Mage::getStoreConfig('homepage/featured_products/featured_promo_btn_link');
        $this->featured_promo_img = Mage::getStoreConfig('homepage/featured_products/featured_promo_img');


        parent::_construct();
    }

}