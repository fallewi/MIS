<?php
/**
 * Copyright (c) 2013 S.L.I. Systems, Inc. (www.sli-systems.com) - All Rights Reserved
 * This file is part of Learning Search Connect.
 * Learning Search Connect is distribute under license,
 * go to www.sli-systems.com/LSC for full license details.
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 * 
 * Translation and systems configuration helper
 * 
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Helper_Data extends Mage_Core_Helper_Abstract {

    const SECTION = "sli_search/";
    const GENERAL_GROUP = "general/";
    const FEED_GROUP = "feed/";
    const FTP_GROUP = "ftp/";
    const FORM_GROUP = "form/";
    const JS_GROUP = "js/";
    const CRON_GROUP = "cron/";
    const ATTR_GROUP = "attributes/";
    const DEFAULT_ATTRS = "default_attributes/";  
    const ENABLED = 1;
    const DISABLED = 2;
    const FEEDENABLED = 3;  

    /**
     * Returns true/false on whether or not the module is enabled
     *
     * @return boolean
     */
    public function isEnabled($store_id = 0) {
        $enabled = Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::GENERAL_GROUP . 'enabled');
        return (bool) ($enabled == self::ENABLED) ? 1 : 0; 
    }

    /**
     * Returns true/false on whether or not the feed is enabled
     *
     * @return boolean
     */
    public function isFeedEnabled($store_id = 0) {
        $feedEnabled = Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::GENERAL_GROUP . 'enabled');
        return (bool) ($feedEnabled != self::DISABLED) ? 1 : 0; 
    }

    /**
     * Returns true/false on whether or not the price feed is enabled
     *
     * @return boolean
     */
    public function isPriceFeedEnabled($store_id = 0) {
        return (bool) Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::GENERAL_GROUP . 'price_feed');
    }    
    
    /**
     * Returns an integer which is the log level
     *
     * @return int
     */
    public function getLogLevel($store_id = 0) {
        return (int) Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::GENERAL_GROUP . 'log_level');
    }    
    
    /**
     * Returns true/false on whether or not we should backup feeds
     *
     * @return boolean
     */
    public function isBackupFeed() {
        return (bool) Mage::getStoreConfig(self::SECTION . self::FEED_GROUP . "backup");
    }

    /**
     * Returns true/false on whether or not we should drop tables
     *
     * @return boolean
     */
    public function isDataPersistent() {
        return (bool) Mage::getStoreConfig(self::SECTION . self::FEED_GROUP . "persistent_data");
    }

    /**
     * Returns true/false on whether or not to include out of stock items in feed
     *
     * @return boolean
     */
    public function isIncludeOutOfStockItems($store_id = 0) {
        return (bool) Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::FEED_GROUP . "stockstatus");
    }    
    
    /**
     * Returns true/false on whether or not to include disabled categories in feed
     *
     * @return boolean
     */
    public function isIncludeDisabledCategories($store_id = 0) {
        return (bool) Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::FEED_GROUP . "categorystatus");
    }   
    
    /**
     * Returns true/false on whether or not we should drop tables;
     *
     * @return int
     */
    public function getWriteBatch() {
        return (int) Mage::getStoreConfig(self::SECTION . self::FEED_GROUP . "write_batch");
    }

    /**
     * Returns true/false on whether or not we should use ftp on feed generation
     *
     * @return boolean
     */
    public function isUseFtp() {
        return (bool) Mage::getStoreConfig(self::SECTION . self::FTP_GROUP . "enabled");
    }

    /**
     * Returns the user by which to log into the ftp server
     *
     * @return string
     */
    public function getFtpUser() {
        return Mage::getStoreConfig(self::SECTION . self::FTP_GROUP . "user");
    }

    /**
     * Returns the password for the user to log into the ftp server
     *
     * @return string
     */
    public function getFtpPass() {
        return Mage::getStoreConfig(self::SECTION . self::FTP_GROUP . "pass");
    }

    /**
     * Returns the host that we will log into via ftp
     *
     * @return string
     */
    public function getFtpHost() {
        return Mage::getStoreConfig(self::SECTION . self::FTP_GROUP . "host");
    }

    /**
     * Returns the path on the ftp server that we will drop the feed into
     *
     * @return string
     */
    public function getFtpPath() {
        return Mage::getStoreConfig(self::SECTION . self::FTP_GROUP . "path");
    }

    /**
     * Returns the javascript that goes into the html head block
     *
     * @return string
     */
    public function getHeaderJs() {
        return Mage::getStoreConfig(self::SECTION . self::JS_GROUP . "header");
    }

    /**
     * Returns the javascript that goes into the before_body_end
     *
     * @return string
     */
    public function getFooterJs() {
        return Mage::getStoreConfig(self::SECTION . self::JS_GROUP . "footer");
    }

    /**
     * Returns the javascript that goes under the mini search form to provide
     * autocomplete features from SLI
     *
     * @return string
     */
    public function getAutocompleteJs() {
        return Mage::getStoreConfig(self::SECTION . self::JS_GROUP . "autocomplete");
    }

    /**
     * Returns the external search domain to the SLI externally hosted search page
     *
     * @return string
     */
    public function getSearchDomain() {
        return Mage::getStoreConfig(self::SECTION . self::JS_GROUP . "domain");
    }

    /**
     * Returns the email to send notifications to when the cron runs
     *
     * @return string
     */
    public function getCronEmail() {
        return Mage::getStoreConfig(self::SECTION . self::CRON_GROUP . "email");
    }

    /**
     * Returns the frequency that the cron should run.
     *
     * @return string
     */
    public function getCronFrequency() {
        return Mage::getStoreConfig(self::SECTION . self::CRON_GROUP . "frequency");
    }

    /**
     * Returns the time of day that the cron should run at.
     *
     * @return string
     */
    public function getCronTime() {
        return Mage::getStoreConfig(self::SECTION . self::CRON_GROUP . "time");
    }

    /**
     * Returns an array of attributes to be included in the feed
     *
     * @return array
     */
    public function getAttributes() {
        $attrs = Mage::getStoreConfig(self::SECTION . self::ATTR_GROUP . "attributes", Mage::app()->getStore()->getId());
        $default_attributes = Mage::getStoreConfig(self::SECTION . self::DEFAULT_ATTRS . "attributes", Mage::app()->getStore()->getId());

        $defaults = array();

        foreach(explode(',',$default_attributes) as $attr) {
            if($attr && trim($attr) != '') $defaults[] = array('attribute'=>trim($attr));
        }

        if($attrs){
            return array_merge($defaults, $attrs);
        }
        else{
            return $defaults;
        }
    }


    /**
     * Return crontab formatted time for cron set time.
     *
     * @param string $frequency
     * @param array $time
     * @return string
     */
    public function getCronTimeAsCrontab($frequency, $time) {
        list($hours, $minutes, $seconds) = $time;
        /**
         * [0] = minutes
         * [1] = hours
         * [2] = day of month
         * [3] = month
         * [4] = day of week
         */
        $cron = array("*", "*", "*", "*", "*");

        //Parse through time
        if ($minutes) {
            $cron[0] = $minutes;
        }

        if ($hours) {
            $cron[1] = $hours;
        }

        //Parse through frequencies
        switch ($frequency) {
            case "3H":
                $cron[1] = '*/3';
                $cron[0] = '00';
                break;
            case "6H":
                $cron[1] = '*/6';
                $cron[0] = '00';
                break;
            case "12H":
                $cron[1] = '*/12';
                $cron[0] = '00';
                break;
            case "W":
                $cron[4] = 0;
                break;
            case "M":
                $cron[2] = 1;
                break;
        }

        return implode(" ", $cron);
    }

    /**
     * Gets the next run date based on cron settings.
     *
     * @return Zend_Date
     */
    public function getNextRunDateFromCronTime() {
        $now = Mage::app()->getLocale()->date();
        $frequency = $this->getCronFrequency();
        list($hours, $minutes, $seconds) = explode(',', $this->getCronTime());

        $time = Mage::app()->getLocale()->date();
        $time->setHour(0)->setMinute(0)->setSecond(0);

        //Parse through frequencies
        switch ($frequency) {
            case "3H":
                if ($time->compare($now) == -1) {
                    while($time->isEarlier($now)){
                        $time->addHour(3);
                    }
                }
                break;
            case "6H":
                if ($time->compare($now) == -1) {
                    while($time->isEarlier($now)){
                        $time->addHour(6);
                    }
                }
                break;
            case "12H":
                if ($time->compare($now) == -1) {
                    while($time->isEarlier($now)){
                        $time->addHour(12);
                    }
                }
                break;
            case "D":
                $time->setHour($hours)->setMinute($minutes)->setSecond($seconds);
                if ($time->compare($now) == -1) {
                    $time->addDay(1);
                }
                break;
            case "W":
                $time->setHour($hours)->setMinute($minutes)->setSecond($seconds);
                $time->setWeekday(7);
                if ($time->compare($now) == -1) {
                    $time->addWeek(1);
                }
                break;
            case "M":
                $time->setHour($hours)->setMinute($minutes)->setSecond($seconds);
                $time->setDay(1);
                if ($time->compare($now) == -1) {
                    $time->addMonth(1);
                }
                break;
        }

        return $time;
    }

    function mtime() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

     /**
     * Returns domain(s) as a js var
     *
     * @return url
     */
    public function getSLIDomainJs() {
        $searchURL = $this->getSearchDomain();
        $scheme = parse_url($searchURL, PHP_URL_SCHEME);
        if (!$scheme) {
            $searchURL = "http://".$searchURL;
        }
        preg_match('/http(s|):\/\/(.+?)\//',$searchURL,$matches);
        $searchURLBase = $searchURL;
        if (isset($matches[2])) {
                $searchURLBase = $matches[2];
        }
        $returnJS = "\n<script type=\"text/javascript\">\nvar slibaseurlsearch = '" . $searchURL ."';\nvar slibaseurl = '" . $searchURLBase . "';\n</script>\n";

        return $returnJS;
    }


    /**
     * Checks to see if the the custom form code should be used
     *
     * @return bool
     */
    public function useCustomForm() {
        return (bool) Mage::getStoreConfig(self::SECTION . self::FORM_GROUP . "customform");
    }


    /**
     * This returns the form code from the DB
     *
     * @return string
     */
    public function getFormData() {
        return Mage::getStoreConfig(self::SECTION . self::FORM_GROUP . "formcode");
    }

	/**
     * Render the cart grand total and total item within the cart
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
	private function _renderCartTotal( $quote )
    {       
        if( !$quote ) return false;
        
        //Declare the array container
        $cartInfoArray = array();
        $quoteItemCount = $quote->getItemsCount();
                
        //Store the item count to array
        $cartInfoArray['NumberOfItems'] = $quoteItemCount;

        $totals = $quote->getTotals();
        if( $totals )
        {
            if( isset($totals['grand_total']) )
                $cartInfoArray['TotalPrice'] = $totals['grand_total']->getValue();
            
            if( isset($totals['tax']) )
                $cartInfoArray['TotalTax'] = $totals['tax']->getValue();            
        }
        
        //Get The Cart Total Discount Amount
        $items = $quote->getAllVisibleItems();
        $itemDiscount = 0;
        foreach( $items as $item )
        {
            if( !$item ) continue;
            $itemDiscount += $item->getDiscountAmount();
        }
        $cartInfoArray['TotalDiscount'] = $itemDiscount;
        
        //Get The Delivery Cost if applicable
        $shippingCost = $quote->getShippingAddress()->getShippingAmount(); 
        $shippingCostTax = $quote->getShippingAddress()->getShippingTaxAmount();
        if($shippingCost == (float)0){
            $cartInfoArray['DeliveryCost'] = 0;
        }else{
            $cartInfoArray['DeliveryCost'] = (float)$shippingCost + (float)$shippingCostTax;
        }
        
        return $cartInfoArray;
    }
    
    /**
     * Render the cart item detail
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    private function _renderItemsDetail( $quote )
    {
        //Array of items
        $itemsArray = array();
        if( !$quote ) return false;

        $items = $quote->getAllVisibleItems();
        
        foreach( $items as $item )
        {
            /** @var $item Mage_Sales_Model_Quote_Item */
            if( !$item ) continue;
            
            //Declare an array to store item information
            $itemInfo = array();
            /** @var $itemProduct Mage_Catalog_Model_Product */
            $itemProduct = $item->getProduct();

            $itemInfo[ 'title' ] = $item->getName();
            $itemInfo[ 'sku' ] = $item->getSku();
            $itemInfo[ 'qty' ] = $item->getQty();
            //Get the item Product Object
            $product = $item->getProduct();
            //Get the original price for item product
            $itemInfo[ 'price' ] = $product->getPrice();
            //Get the sale price      
            $itemInfo[ 'sale_price' ] = $item->getPriceInclTax(); 
            
            $itemInfo[ 'item_url' ] = $this->getItemUrl($item);
            $itemInfo[ 'remove_url' ] = Mage::getUrl('checkout/cart/delete/', array('id'=>$item->getId()));            
            $itemInfo[ 'image_url' ] = Mage::getModel('catalog/product_media_config')->getMediaUrl($itemProduct->getThumbnail());

            $itemsArray[] = $itemInfo;
        }        
        return $itemsArray;
    }

    /**
     * Get the item url
     * @param $item Mage_Sales_Model_Quote_Item
     * @return string
     */
    public function getItemUrl($item)
    {
        if ($item->getRedirectUrl()) {
            return $item->getRedirectUrl();
        }
        
        $product = $item->getProduct();
        $option  = $item->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }
        return $product->getUrlModel()->getUrl($product);
            
    }
    
    
    /**
     * Render the JSONP object for SLI
     * 
     * @param Mage_Sales_Model_Quote $quote
     * @return string
     */
    public function getCartJSONP( $quote )
    {
    	$key_values['form_key']     = Mage::getSingleton('core/session')->getFormKey();
        $key_values['logged_in']    = Mage::getSingleton('customer/session')->isLoggedIn();
        $key_values['user_name']    = $this->escapeHtml(Mage::getSingleton('customer/session')->getCustomer()->getName());

        $cart = $this->_renderCartTotal( $quote );
        $items['items'] = $this->_renderItemsDetail( $quote );
        
        $result = array_merge($key_values, $cart, $items);
        $jsonResult = json_encode( $result );        
        //Wrap up as jsonp object
        $jsonpResult = "sliCartRequest($jsonResult)";
        return $jsonpResult;
    }

}