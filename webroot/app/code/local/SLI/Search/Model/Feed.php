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
 * Generates feed file based on store
 *
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Model_Feed extends Mage_Core_Model_Abstract {
    //These may look like magic numbers but they are sampled averages for
    //memory management based on a statistical analysis of several data sets.

    const PRODUCTS_TO_LOAD = 20000; //Base total products to load at a time
    const ATTRIBUTE_MARGIN_THRESHOLD = 20; //# of attributes before reduce load
    const ATTRIBUTE_PRODUCT_REDUCTION = 1000; //# of products to reduce total by
    
    public  $_ajaxNotice = "Currently generating feeds...";
    protected $_isLog = true;
    protected $_logFile = null;
    protected $_dbConnection = null;
    protected $_tableNames = array();
    protected $_totalProductCount = null;
    protected $_productLoadStep = null;
    protected $_startTime = null;
    protected $_categoryNameAttribute = null;
    protected $_loadedCategoryCache = array();
    protected $_productSelect = null;
    protected $_feedFileHandle = null;
    protected $_feedFileName = null;
    protected $_feedLockFile = null;
    protected $_feedArchivePath = null;
    protected $_feedBackupPath = null;
    protected $_storeId = null;
    protected $_websiteId = null;
    protected $_originalStoreId = null;
    protected $_currStoreId = null;
    protected $_currStoreName = null;
    protected $_attributes = null;
    protected $_useCategories = false;
    protected $_views = null;
    protected $_viewNumber = 0;
    protected $_primaryView;
    protected $_primaryViewNum;
    protected $_boundaries;
    protected $_memoryLimit = null;
    protected $_warningMemoryLimit = null;
    protected $_errorMemoryLimit = null;

    const VIEW_ATTR_LIMIT = 5;
    const WARNING_MEMORY_LIMIT = 75;
    const ERROR_MEMORY_LIMIT = 90;

    // Some attributes will need separate queries
    protected $_seperateQueryAttributes = array(
        'parent_id',
    );
    protected $_priceAttributesSet = null;
    protected $_priceAttributes = array(
        'minimal_price',
        'min_price',
        'max_price',
        'tier_price',
        'price',
        'final_price',
            //DON'T PUT 'special_price' HERE, WILL ACTUALLY REMOVE IT FROM FEED
    );
    protected $_complexProductTypes = array(
        'configurable',
        'bundle',
        'grouped',
    );
    //Special attributes for which we DON'T want to join other options against
    protected $_specialAttributes = array(
        'visibility',
        'parent_id',
        'has_options',
    );
    protected $_complexProductIds = array();

    /**
     * Generate feed based on store and returns success or error message
     *
     * @param bool $price_feed
     *
     * @return bool|string
     * @throws SLI_Search_Exception
     */
    public function generateFeed($price_feed = false) {
        try {
            $this->_startTime = Mage::helper('sli_search')->mtime();
            $this->_feedLockFile = null;
            $this->_setupMemoryLimits();
            $this->_setupFeed($price_feed);

            if ($price_feed) {
                $this->_log("Generating price feed.....", 2);

                $this->_buildPriceFeed();

                $this->_log("Finished generating price feed.....", 2);
            } else {
                $this->_log("Adding products to feed.....", 2);

                $this->_addProductsToFeed();

                $this->_log("Finished adding products to feed.......", 2);

                //Removed per client request
                //$this->_addChildProducts();
            }

            $this->_tearDownFeed($price_feed);

            if (Mage::helper('sli_search')->isUseFtp()) {
                $this->_sendFeedFile($price_feed);
            }

            $this->_log("--Finished--", 3);
            return true;
        } catch (SLI_Search_NotificationException $e) {
            $this->_log("SLI_Search_NotificationException: {$e->getMessage()}", 1);
            $this->_unlockFeedGeneration();
            return $e->getMessage();
        } catch (SLI_Search_Exception $e) { // Catch SLI_Search_Exception
            $this->_log("SLI_Search_Exception: {$e->getMessage()}", 1);
            $this->_unlockFeedGeneration();
            throw new SLI_Search_Exception( $e->getMessage());
        } catch (Exception $e) {
            $this->_log("Exception: {$e->getMessage()}", 1);
            Mage::log("Exception: {$e->getMessage()}");
            $this->_unlockFeedGeneration();
            throw new SLI_Search_Exception( $e->getMessage());
        }
    }

    /**
     * Provide all front end loading for the feed generation
     *
     * @param bool $price_feed
     *
     * @throws Mage_Core_Exception
     * @throws SLI_Search_Exception
     */
    protected function _setupFeed($price_feed = false) {
        if ($this->_feedIsLocked($price_feed)) {
            $msg = "Generation temporarily locked. Feed already being generated.";
            $this->_log($msg, 1);
            throw new SLI_Search_Exception($msg);
        }
        $this->_configurationSettings();

        $storeId = $this->_getStoreId();
        $this->_currStoreId = $storeId;

        $this->_currStoreName = $this->_getStoreName($storeId);

        $this->_log("Store id = " . $storeId . "<<<\n", 2);

        $this->_originalStoreId = Mage::app()->getStore()->getId();

        Mage::app()->getStore()->setId($storeId);

        if (!Mage::helper('sli_search')->isFeedEnabled($this->_currStoreId)) {
            $this->_log("Feed not enabled for store $storeId", 1);
            throw new SLI_Search_NotificationException("Feed not enabled for store $storeId");
        }

        if ($price_feed && !Mage::helper('sli_search')->isPriceFeedEnabled($this->_currStoreId)) {
            $this->_log("Price feed not enabled for store $storeId", 1);
            throw new SLI_Search_NotificationException("Price feed not enabled for store $storeId");
        }

        $this->_checkWritePermissions($price_feed);

        if (Mage::helper('sli_search')->isBackupFeed()) {
            $this->_backUpFeeds($price_feed);
        }

        $this->_lockFeedGeneration();

        $this->_log("--Setup... Store $storeId--", 2);
        $additional_attribs = $this->getProductsInfoAttributes();
        if ($price_feed)
            $this->_writeToFeedFile("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<prices".$additional_attribs.">", $price_feed);
        else
            $this->_writeToFeedFile("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<products".$additional_attribs.">", $price_feed);
    }

    /**
     * Additional data needed by SLI, added per request
     * @return string
     */
    private function getProductsInfoAttributes() {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $info = $modules->SLI_Search->asArray();
        $lsc_version = isset($info['version']) ? $info['version'] : 'UNKNOWN';
        $mage_version = Mage::getVersion();
        $server_name = (isset($_SERVER['SERVER_NAME']))?$_SERVER['SERVER_NAME']:'CLI';
        $self_script = $_SERVER['PHP_SELF'];
        $m_base_url = Mage::getBaseUrl();
        $m_curr_url = Mage::helper('core/url')->getCurrentUrl();
        $creation_date = date("c");
        $ret_val = " storeid=\"" . $this->_currStoreId . "\" store_name=\"" . $this->_currStoreName . "\" server=\"" . $server_name . "\" mage_version=\"" . $mage_version .
            "\" lsc_version=\"" . $lsc_version . "\" m_curr_url=\"" . $m_curr_url . "\" m_base_url=\"" .$m_base_url .
            "\" self_script=\"" . $self_script . "\"" . " creation_date=\"" . $creation_date . "\" ";
        return $ret_val;
    }

    /**
     * Provide all back management for the feed generation
     *
     * @param bool $price_feed
     */
    protected function _tearDownFeed($price_feed = false) {
        if ($price_feed)
            $this->_writeToFeedFile("</prices>", $price_feed);
        else
            $this->_writeToFeedFile("</products>", $price_feed);
        $this->_closeFeedFileHandle();

        $this->_log("--Feed Generated--", 2);

        Mage::app()->getStore()->setId($this->_originalStoreId);
        $this->_unlockFeedGeneration();
    }

    /**
     * Create price feed
     */
    protected function _buildPriceFeed() {
//        Mage::app()->setCurrentStore(1);
        $helper = Mage::helper('sli_search');
        $batch = $helper->getWriteBatch();
        $select = $this->_generatePriceFeedSql();

        //Array of parents price adjustments
        $priceAdjustments = $this->_getRelationalPriceAdjustments($select);

        if (!$this->_boundaries) {
            $query = "select min(entity_id) as min_pid, max(entity_id) as max_pid from {$this->_getTable("catalog/product_index_price")}";
            $result = $this->_getConnection()->query($query);
            if (!$result)
                Mage::throwException("Unable to determine boundaries for price feed. Query: {$query}");
            $boundaries = $result->fetchAll();
            $this->_boundaries = array();
            $this->_boundaries['min'] = $boundaries[0]['min_pid'];
            $this->_boundaries['max'] = $boundaries[0]['max_pid'];
        }

        $offset = 0;
        if($batch < 1) $batch = 15;

        for ($i = 0; $offset < $this->_boundaries['max']; $i++) {
            $offset = $i * $batch + $this->_boundaries['min'];
            $endBatch = $offset + $batch - 1;
            $query = $select . " AND entity_id BETWEEN {$offset} AND {$endBatch} ORDER BY ENTITY_ID";

            $this->_log($query, 3);

            $begin_qtime = $helper->mtime();
            $resource = $this->_getConnection()->query($query);
            $qtime = (float) $helper->mtime() - (float) $begin_qtime;
            $this->_log("Query execution:  " . $qtime . " seconds", 3);

            $buffer = '';
            if ($resource) {
                $this->_log("Adding products to price feed.", 2);
                $dataSet = $resource->fetchAll(PDO::FETCH_ASSOC);
                $parseTime = $helper->mtime();
                if (count($dataSet) > 0) {
                    foreach ($dataSet as $data) {
                        $buffer .= $this->_organizePriceData($data, $priceAdjustments);
                    }
                    $ptime = (float) $helper->mtime() - (float) $parseTime;
                    $this->_log("Parsing the product price data set took: {$ptime}", 3);
                    $begin_wtime = $helper->mtime();
                    $this->_writeToFeedFile($buffer, true);
                    $wtime = (float) $helper->mtime() - (float) $begin_wtime;
                    $this->_log("Writing product price data to disk took: {$wtime}", 3);
                }
            } else {
                $this->_log("Error: There was a problem with the database query. Unable to write feed to file: {$select}", 1);
            }
        }
    }

    protected function _generatePriceFeedSql(){
        $master_fields = "cprice.entity_id as product_id, cprice.customer_group_id as customer_group_id, cprice.website_id as website_id, customer_group_code, cprice.tax_class_id, price, final_price, min_price, max_price, tier_price";
        if (method_exists('Mage', 'getEdition') && Mage::getEdition() == "Enterprise")
            $master_fields .= ", group_price";
        $super_attrs = "super_attrs.attribute_id, super_attrs.pricing_value, super_attrs.is_percent, super_attrs.value_index, super_attrs.option_id, super_attrs.sort_order, super_attrs.value as label, attr_label";
        $minor_fields = "product_super_attribute_id, product_id, eao.attribute_id, pricing_value, is_percent, website_id, value_index, option_id, sort_order, value, attr_label";
        $super_id_fields = "sa.product_super_attribute_id, sa.product_id, sa.attribute_id, sp.pricing_value, sp.is_percent, sp.website_id, sp.value_index, sp.value_id";
        $eav_fields = "ea.option_id, ea.sort_order, ifnull(eal.value, ea.frontend_label) as value, ea.attribute_id, ifnull(eaov2.value, eaov.value) as attr_label";
        $ea_fields = "eao.option_id, ea.frontend_label, ea.attribute_id, eao.sort_order";
        $select = "	select {$master_fields}, {$super_attrs} 
        			from {$this->_getTable("catalog/product_index_price")} cprice
                    left outer join {$this->_getTable("customer_group")} cg on cg.customer_group_id = cprice.customer_group_id
                    left outer join
                    (
                    	select {$minor_fields} 
                    	from 
                    	(
                    		select {$super_id_fields} 
                    		from {$this->_getTable("catalog/product_super_attribute")} sa, {$this->_getTable("catalog/product_super_attribute_pricing")} sp
                            where sa.product_super_attribute_id = sp.product_super_attribute_id
                        ) sap
                        left outer join
                        (
                        	select {$eav_fields}
                        	from
                        	(
                        		select {$ea_fields}
                        		from {$this->_getTable("eav/attribute_option")} eao, {$this->_getTable("eav/attribute")} ea
                        		where eao.attribute_id = ea.attribute_id
                        	) ea
							left outer join
							{$this->_getTable("eav/attribute_label")} eal
							on eal.attribute_id = ea.attribute_id and eal.store_id = {$this->_getStoreId()}
							left outer join
							{$this->_getTable("eav/attribute_option_value")} eaov
							on eaov.option_id = ea.option_id and eaov.store_id = 0
							left outer join
							{$this->_getTable("eav/attribute_option_value")} eaov2
							on eaov2.option_id = ea.option_id and eaov2.store_id = {$this->_getStoreId()}
                       	) eao
                        on eao.option_id = sap.value_index
                    ) super_attrs
                    on cprice.entity_id = super_attrs.product_id 
                    where cprice.website_id = {$this->_websiteId}";

        return $select;
    }

    protected function _getRelationalPriceAdjustments($select){
        $priceUpdates = array();

        //Get Array of parent ids
        $parentQuery = "SELECT distinct parent_id from catalog_product_relation;";

        $resource = $this->_getConnection()->query($parentQuery);
        if($resource){
            $parents = $resource->fetchAll(PDO::FETCH_ASSOC);

            for($x=0; $x<count($parents); $x++){
                $parents[$x] = $parents[$x]['parent_id'];
            }
            $implodedParents = implode('","', $parents);

            //Get related Catalog Price Rules
            $cprSelect = "select customer_group_id, product_id, sub_simple_action, sub_discount_amount from catalogrule_product where product_id in(\"{$implodedParents}\") and website_id = 1;";

            $resource = $this->_getConnection()->query($cprSelect);

            if($resource){
                $cprData = $resource->fetchAll(PDO::FETCH_ASSOC);
            }

            //Use the *main* query to get required price values.  If slow, we can probably write a smaller query that will be faster.
            $query = $select . " and entity_id in(\"{$implodedParents}\");";
            $resource = $this->_getConnection()->query($query);

            if($resource){
                $dataSet = $resource->fetchAll(PDO::FETCH_ASSOC);

                foreach($dataSet as $data){
                    $parent = $data['product_id'];
                    $customerGroupCode = $data['customer_group_code'];

                    //Calculate Minimum
                    if(isset($priceUpdates[$parent][$customerGroupCode]['min_price'])){
                        if($priceUpdates[$parent][$customerGroupCode]['min_price'] > $data['min_price']){
                            $priceUpdates[$parent][$customerGroupCode]['min_price'] = $data['min_price'];
                        }
                    }
                    else{
                        $priceUpdates[$parent][$customerGroupCode]['min_price'] = $data['min_price'];
                    }

                    //Calculate Maximum
                    $percentDiscount = 0;
                    $staticDiscount = 0;

                    foreach($cprData as $cpr){
                        if($cpr['product_id'] == $parent){
                            if($data['customer_group_id'] == $cpr['customer_group_id']){
                                if($cpr['sub_simple_action'] == 'by_percent'){
                                    $percentDiscount += $cpr['sub_discount_amount'];
                                }
                                else{
                                    $staticDiscount += $cpr['sub_discount_amount'];
                                }
                            }
                        }
                    }
                    if($percentDiscount > 0){
                        $add = $data['pricing_value'] - ($data['pricing_value'] * ($percentDiscount/100));
                    }
                    else{
                        $add = $data['pricing_value'];
                    }

                    $add -= $staticDiscount;

                    if($priceUpdates[$parent][$customerGroupCode]['max_price'] < $data['min_price'] + $add){
                        $priceUpdates[$parent][$customerGroupCode]['max_price'] = $data['min_price'] + $add;
                    }
                }
            }
        }
        return $priceUpdates;
    }

    /**
     * Add the currently set range of products to the feed
     */
    protected function _addProductsToFeed() {
        $select = $this->_getProductSelect();
        $helper = Mage::helper('sli_search');
        $batch = $helper->getWriteBatch();

        if (!$this->_boundaries) {
            $query = "select min(product_id) as min_pid, max(product_id) as max_pid from {$this->_primaryView}";
            $result = $this->_getConnection()->query($query);
            if (!$result)
                Mage::throwException("Unable to determine boundaries (sliview{$this->_getStoreId()}_{$viewNumber}). Query: {$query}");
            $boundaries = $result->fetchAll();
            $this->_boundaries = array();
            $this->_boundaries['min'] = $boundaries[0]['min_pid'];
            $this->_boundaries['max'] = $boundaries[0]['max_pid'];
        }

        $offset = 0;
        for ($i = 0; $offset < $this->_boundaries['max']; $i++) {
            $offset = $i * $batch + $this->_boundaries['min'];
            $endBatch = $offset + $batch - 1;
            $query = $select . " WHERE s{$this->_primaryViewNum}.product_id BETWEEN {$offset} AND {$endBatch}";

            $this->_log("Final write query:  " . $query, 3);

            $begin_qtime = $helper->mtime();
            $resource = $this->_getConnection()->query($query);
            $qtime = (float) $helper->mtime() - (float) $begin_qtime;
            $this->_log("Query execution:  " . $qtime . " seconds", 3);

            $buffer = '';
            if ($resource) {
                $this->_log("Adding products.", 2);
                $dataSet = $resource->fetchAll();
                $parseTime = $helper->mtime();
                if (count($dataSet) > 0) {
                    foreach ($dataSet as $data) {
                        if (isset($data['type_id']) && in_array($data['type_id'], $this->_complexProductTypes)) {
                            $this->_complexProductIds[] = isset($data['product_id']) ? $data['product_id'] : '';
                        }
                        $buffer .= $this->_getProductXml($data);
                    }
                    $ptime = (float) $helper->mtime() - (float) $parseTime;
                    $this->_log("Parsing the product data set took: {$ptime}", 3);
                    $begin_wtime = $helper->mtime();
                    $this->_writeToFeedFile($buffer);
                    $wtime = (float) $helper->mtime() - (float) $begin_wtime;
                    $this->_log("Writing product data to disk took: {$wtime}", 3);
                }
            } else {
                $this->_log("Error: There was a problem with the database query. Unable to write feed to file: {$select}", 1);
            }
        }
    }

    /**
     * Formats price data
     */
    protected function _organizePriceData($data, $priceAdjustments) {
        $prod_id = utf8_encode(htmlspecialchars($data['product_id'], ENT_QUOTES));
        $group_code = utf8_encode(htmlspecialchars($data['customer_group_code'], ENT_QUOTES));
        $price = utf8_encode(htmlspecialchars($data['price'], ENT_QUOTES));
        $final_price = utf8_encode(htmlspecialchars($data['final_price'], ENT_QUOTES));
        $min_price = utf8_encode(htmlspecialchars($data['min_price'], ENT_QUOTES));
        $max_price = utf8_encode(htmlspecialchars($data['max_price'], ENT_QUOTES));
        $tier_price = utf8_encode(htmlspecialchars($data['tier_price'], ENT_QUOTES));

        if (method_exists('Mage', 'getEdition') && Mage::getEdition() == "Enterprise")
            $group_price = utf8_encode(htmlspecialchars($data['group_price'], ENT_QUOTES));
        else
            $group_price = '';

        $attribute_label = utf8_encode(htmlspecialchars($data['attr_label'], ENT_QUOTES));
        $option_label = utf8_encode(htmlspecialchars($data['label'], ENT_QUOTES));
        $price_type = utf8_encode(htmlspecialchars($data['is_percent'], ENT_QUOTES));
        $option_price = utf8_encode(htmlspecialchars($data['pricing_value'], ENT_QUOTES));

        if(isset($priceAdjustments[$data['product_id']][$group_code])){
            if(isset($priceAdjustments[$data['product_id']][$group_code]['min_price']))
                $min_price = $priceAdjustments[$data['product_id']][$group_code]['min_price'];
            if(isset($priceAdjustments[$data['product_id']][$group_code]['max_price']))
                $max_price = $priceAdjustments[$data['product_id']][$group_code]['max_price'];
        }

        $option_prices_set = "<option><label>{$option_label}</label><is_price_percentage>{$price_type}</is_price_percentage><price>{$option_price}</price></option>";
        $option_prices_set = "<option_prices><attribute><label>{$attribute_label}</label>{$option_prices_set}</attribute></option_prices>";

        $product_prices_set = "<customer_group><group_code>{$group_code}</group_code><price>{$price}</price><final_price>{$final_price}</final_price><min_price>{$min_price}</min_price><max_price>{$max_price}</max_price><tiered_price>{$tier_price}</tiered_price><group_price>{$group_price}</group_price></customer_group>";
        $product_prices_set = "<product><product_id>{$prod_id}</product_id>{$product_prices_set}{$option_prices_set}</product>";

        return $product_prices_set . PHP_EOL;
    }

    /**
     * Add child products of complex products to the feed.
     * Because of the visibility filters added to get the products in the correct store,
     * we cannot add the children in with the rest of the products.
     */
    protected function _addChildProducts() {
        $parents = implode(',', $this->_complexProductIds);
        if (!$parents)
            return;
        $query = "SELECT parent_id, child_id from catalog_product_relation where parent_id in ({$parents})";

        $this->_log("Child sql query....$query", 3);

        $result = $this->_getConnection()->query($query);

        if ($result) {
            $children = $result->fetchAll(PDO::FETCH_ASSOC);

            foreach ($children as $key => $subArr) {
                $children[$key] = isset($subArr['child_id']) ? $subArr['child_id'] : '';
            }

            $select = $this->_getProductSelectChildren($children);

            $this->_log("loading extra children.............", 3);

            $resource = $this->_getConnection()->query($select);

            $this->_log("retrieved result object for children.............", 3);

            $buffer = '';
            if ($resource) {
                $buffer .= "<children>";
                $this->_log("Adding Children...", 2);
                while ($data = $resource->fetch(PDO::FETCH_ASSOC)) {
                    $buffer .="<child>" . $this->_getProductXml($data) . "</child>";
                }
                $buffer .= "</children>";
                $this->_writeToFeedFile($buffer);
            } else {
                $this->_log("Error: There was a problem with the database query. Unable to write feed to file: {$select}", 1);
            }
        }
    }

    /**
     * Get the select statement for the children of complex products.
     *
     * @param array $children
     * @return string
     */
    protected function _getProductSelectChildren($children) {
        /* @var $products Mage_Catalog_Model_Resource_Product_Collection */

        $this->_viewNumber = 0;
        $views = $this->_getViews();
        foreach ($views as $key => $view) {
            $this->_log("View Data:  $key => " . print_r($view, 1), 3);

            $products = Mage::getResourceModel('catalog/product_collection')
                    ->addAttributeToFilter('status', 1);
            if (!Mage::helper('sli_search')->isIncludeOutOfStockItems($this->_currStoreId))
                Mage::getModel('cataloginventory/stock')->addInStockFilterToCollection($products);

            $products->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns(array('product_id' => 'entity_id'));

            $childrenList = implode(',', $children);
            $products->getSelect()->where("e.entity_id in ({$childrenList})");

            $this->_addView($products, $view, $key);
        }

        $this->_log("Get select from views.....", 3);
        $this->_productSelect = $this->_getSelectFromViews();
        $this->_log("Select created.....", 3);

        return $this->_productSelect;
    }

    /**
     * Returns the select statement with all attributes for the feed joined
     * and any preconditions set
     * @return Zend_Db_Select
     */
    protected function _getProductSelect() {
        /* @var $products Mage_Catalog_Model_Resource_Product_Collection */

        $this->_viewNumber = 0;
        $views = $this->_getViews();
        $sid = $this->_getStoreId();
        
        $this->_log("Views" . PHP_EOL . print_r($views, 1), 2);

        foreach ($views as $key => $view) {
            $this->_log("View Data:  $key => " . PHP_EOL . print_r($view, 1), 3);
            //Extra setting to work around flat tables not being able to change the store
            Mage::getResourceModel('catalog/product_collection')->setStore($sid);
            $products = Mage::getResourceModel('catalog/product_collection')
                    ->addAttributeToFilter('status', 1);
            if (!Mage::helper('sli_search')->isIncludeOutOfStockItems())
                Mage::getModel('cataloginventory/stock')->addInStockFilterToCollection($products);

            /* removed per client request
              Mage::getSingleton('catalog/product_visibility')
              ->addVisibleInCatalogFilterToCollection($products)
              ->addVisibleInSearchFilterToCollection($products);
             *
             */
            
            $products->addStoreFilter($sid);
            $products->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns(array('product_id' => 'entity_id'));
            $this->_addView($products, $view, $key);
        }

        $this->_log("Get select from views as product select..........................", 3);
        $this->_productSelect = $this->_getSelectFromViews();

        return $this->_productSelect;
    }

    /**
     * Get the mysql view groups of the product attributes
     *
     * @return array
     */
    protected function _getViews() {

        if (is_null($this->_views)) {
            $views = array();

            // Make array of attributes we need to price group or generate alone.
            $separateAttributes =  $this->_priceAttributes;

            // Remove attributes which we must generate in separate views.
            $attributes = array_diff($this->_getAttributes(), $separateAttributes);

            while (count($attributes)) {
                $views[] = array_splice($attributes, 0, self::VIEW_ATTR_LIMIT);
            }

            // Add solo view attributes to their own view if they have been selected.
            $priceView = array();

            foreach ($separateAttributes as $attr) {
                // If separated attribute is selected.
                if (in_array($attr, $this->_getAttributes())) {
                    // If the separated view is actually a price element add it to the Pricing View.
                    if(in_array($attr, $this->_priceAttributes)){
                        array_push($priceView, $attr);
                    }
                }
            }

            array_push($views, $priceView);

            $this->_views = $views;
        }
        return $this->_views;
    }

    /**
     * Create a view for a set of attributes
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $products
     * @param array $view
     * @param string $viewNumber
     */
    protected function _addView($products, $view, $viewNumber) {
        foreach ($view as $attribute) {
            $this->_addAttributeToProductCollection($products, $attribute);
        }

        $select = $products->getSelectSql(true) . " GROUP BY e.entity_id";//PRODUCT_ID";

        $table = "sliview{$this->_getStoreId()}_{$viewNumber}";
        $query = "DROP TABLE IF EXISTS {$table}; CREATE TABLE {$table} as {$select}; ALTER TABLE {$table} MODIFY product_id INT(10) UNSIGNED PRIMARY KEY;";
        $result = $this->_getConnection()->query($query);

        $this->_log("Creating mediary data sets:  {$query}", 3);

        if ($result === false) {
            Mage::throwException("Unable to create MySQL view (sliview{$this->_getStoreId()}_{$viewNumber}). Query: {$query}");
        }

        $this->_viewNumber += 1;
    }

    /**
     * Generate a sql select statement from the existing views
     *
     * @return string
     */
    protected function _getSelectFromViews() {

        $fields = '';
        $joins = '';

        // If there are no views, there was an error. Return nothing.
        if ($this->_viewNumber == 0) {
            return '';
        }

        $this->_log("Bulding up select from view...", 3);

        $nameContainer = array();

        $viewResult = $this->_getConnection()->query("SHOW TABLES like 'sliview{$this->_getStoreId()}_%'");

        if (!$viewResult) {
            Mage::throwException ("View {$viewResult} does not exist");
        }
        $numViews = count($this->_views);
        $this->_log("Number of views in DB: {$viewResult->rowCount()} - Expected: {$numViews}", 3);
        if($viewResult->rowCount() != $numViews) {
            Mage::throwException ("View Generation failed, expected:{$numViews} got:{$viewResult->rowCount()} views."
            . " Please check php and SQL settings");
        }
        $viewResultData = $viewResult->fetchAll();
        sort($viewResultData);
        $this->_primaryView = null;

        foreach ($viewResultData as $k => $viewData) {
            //viewData is a two item array, 0 being the view name
            $viewName = $viewData[0];
            $pos = strpos($viewName, '_') + 1;
            $viewNum = substr($viewName, $pos);

            if (stristr($viewName, 'sliview') === false) {
                unset($viewResultData[$k]);
                continue;
            }

            if (!$this->_primaryView)
                $this->_primaryView = $viewData[0];

            $query = "DESC {$viewData[0]}";

            $result = $this->_getConnection()->query($query);

            foreach ($result->fetchAll() as $column) {
                if (isset($column['Field']) && !isset($nameContainer[$column['Field']])) {
                    $nameContainer[$column['Field']] = "s{$viewNum}.{$column['Field']}";
                }
            }
        }
        $this->_primaryViewNum = substr($this->_primaryView, -1);
        $fields .= implode(', ', $nameContainer);

        // Get the views to join on
        foreach ($viewResultData as $viewData) {
            $viewName = $viewData[0];
            $pos = strpos($viewName, '_') + 1;
            $viewNum = substr($viewName, $pos);
            
            if ($viewName == $this->_primaryView)
                continue;
            $joins .= "LEFT JOIN {$viewName} AS s{$viewNum} ON s{$this->_primaryViewNum}.product_id = s{$viewNum}.product_id ";
        }

        // Generate the select statement
        // We can assume that there is at least one view by this point
        $sql = "SELECT {$fields} FROM {$this->_primaryView} AS s{$this->_primaryViewNum} {$joins}";

        $this->_log("Created select from view $sql...", 3);

        return $sql;
    }

    /**
     * Adds a single attribute to the product collection based on code
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $products
     * @param string $code
     */
    protected function _addAttributeToProductCollection($products, $code) {
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode("catalog_product", $code);
        $inventoryPrefix = SLI_Search_Model_System_Config_Source_Attributes::INVENTORY_ATTRIBUTES_PREFIX;
        $reviewPrefix = SLI_Search_Model_System_Config_Source_Attributes::REVIEW_ATTRIBUTES_PREFIX;
        $linkedPrefix = SLI_Search_Model_System_Config_Source_Attributes::LINKED_PRODUCTS_PREFIX;
        $sid = $this->_getStoreId();
        
        if (in_array($code, $this->_priceAttributes)) {

            if (is_null($this->_priceAttributesSet)) {
                $products->addPriceData();
                $this->_priceAttributesSet = true;
            }
        } else if ($attribute && $attribute->getId()) {

            if ($attribute->getBackendType() != 'static') {
                try {
                    $products->joinAttribute($code, "catalog_product/$code", 'entity_id', null, 'left', $sid);
                } catch (Exception $e) {
                    //Keep going. Build with partial data if need be
                }
            } else {
                $products->getSelect()->columns(array($code => "e.$code"));
            }

            if ($attribute->usesSource()) {
            	//Check if the model is boolean
            	if($attribute->getSourceModel() == "eav/entity_attribute_source_boolean") {
	    			$attribute_id = $attribute->getData('attribute_id');
	    			//If it is set to global grab the global value and join the table
	    			if($attribute->getData('is_global') == 1){
	                    $products->getSelect()->joinLeft(
	                        array(
	                            "opt_$code" => $this->_getTable("catalog_product_entity_int")),
	                            "(opt_$code.entity_id = e.entity_id) AND (opt_$code.attribute_id = '$attribute_id') AND (opt_$code.store_id = 0)",
	                            array("option_$code" => "IF(at_$code.value IS NULL, NULL, IF(at_$code.value = 1, 'Yes', NULL))")
	                    );
	                }
	                else{
	                	//Join the individual store specific value for the Yes/No otherwise use the default value
	                    $products->getSelect()->joinLeft(
	                        array(
	                            "opt_$code" => $this->_getTable("catalog_product_entity_int")),
	                            "(opt_$code.entity_id = e.entity_id) AND (opt_$code.attribute_id = '$attribute_id') AND (opt_$code.store_id = $sid)",
	                            array("option_$code" => "IF(at_{$code}.value_id > 0, IF(at_{$code}.value IS NULL, NULL, IF(at_{$code}.value = 1, 'Yes', NULL)), 
	                            	IF(at_{$code}_default.value IS NULL, NULL, IF(at_{$code}_default.value = 1, 'Yes', NULL)))")
	                    );
	                }
	    		//Non boolean 	
	            }else {
	            	if($attribute->getData('is_global') == 1){

	                    $products->getSelect()->joinLeft(
	                        array(
	                            "opt_$code" => $this->_getTable("eav/attribute_option_value")),
	                            "concat(',',at_$code.value,',') like concat('%,',opt_$code.option_id,',%')",
	                            array("option_$code" => "group_concat(DISTINCT opt_$code.value SEPARATOR '|')")
	                    );
	                }
	                else{
	                    $products->getSelect()->joinLeft(
	                        array(
	                            "opt_$code" => $this->_getTable("eav/attribute_option_value")),
	                            "concat(',',IF(at_{$code}.value_id > 0, at_{$code}.value, at_{$code}_default.value),',') like concat('%,',opt_$code.option_id,',%')",
	                            array("option_$code" => "group_concat(DISTINCT opt_$code.value SEPARATOR '|')")
	                    );
	                }
	            }
            }
        } else if (substr($code, 0, strpos($code, '_')) == $inventoryPrefix) {
            // this attribute has the inventory prefix

            $code = substr($code, strlen($inventoryPrefix . '_'));
            $products->getSelect()->joinLeft(
                    array("inv_$code" => $this->_getTable('cataloginventory/stock_item')), "inv_$code.product_id = e.entity_id", array("inventory_$code" => "inv_$code.$code")
            );
        } else if (substr($code, 0, strpos($code, '_')) == $reviewPrefix) {
            // this attribute has the review prefix

            $code = substr($code, strlen($reviewPrefix . '_'));
            $products->getSelect()->joinLeft(
                array(
                    "rev_ent_sum_store_{$code}" => $this->_getTable('review/review_aggregate')),
                    "rev_ent_sum_store_{$code}.entity_pk_value = e.entity_id and rev_ent_sum_store_{$code}.store_id = {$sid}",
                    array("review_{$code}" => "rev_ent_sum_store_{$code}.$code")
            );
        } else if (substr($code, 0, strpos($code, '_')) == $linkedPrefix) {
            // this attribute has the linked prefix

            $code = substr($code, strlen($linkedPrefix . '_'));
            $linkType = $this->_getLinkTypes($code);
            $products->getSelect()->joinLeft(
                array(
                    "linked_{$code}" => $this->_getTable('catalog/product_link')),
                    "linked_{$code}.product_id = e.entity_id AND linked_{$code}.link_type_id = {$linkType}",
                    array("linked_{$code}" => "group_concat(DISTINCT linked_{$code}.linked_product_id SEPARATOR ',')")
            );
        } else if (!in_array($code, $this->_seperateQueryAttributes)) {

            // If we are working with non-eav attributes, we must add them in differently.
            // We must also check to be sure that attribute isn't already added in

            $columns = $products->getSelect()->getPart('columns');
            if (!$this->_inArrayRecursive($code, $columns)) {
                $products->getSelect()->columns(array($code => "e.$code"));
            }
        }
    }

    /**
     * Recursively search through an array for a value
     *
     * @param $needle
     * @param $haystack
     * @param bool $strict
     * @return bool
     */
    protected function _inArrayRecursive($needle, $haystack, $strict = true) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->_inArrayRecursive($needle, $item, $strict))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the feed xml in a string given the product data
     *
     * @param array $data
     * @return string
     */
    protected function _getProductXml($data) {

        if (!$data || empty($data)) {
            return '';
        }

        $data = $this->_setDummyData($data);
        $productLinkedData = '';
        $productId = (isset($data['product_id'])) ? $data['product_id'] : null;
        if ($productId == null)
            return;

        $xml = "<product>";
        foreach ($this->_getAttributes() as $attribute) {
            if (array_key_exists($attribute, $data)) {
                $value = $data[$attribute];
                if (array_key_exists("option_$attribute", $data) && !in_array($attribute,$this->_specialAttributes)) {
                    if ($data["option_$attribute"] != null && !is_int($data["option_$attribute"]))
                        $value = $data["option_$attribute"];
                    else
                        $value = null; //if option_$attribute exists but doesn't have a value, it might have been deleted and the id lingers
                }
                if($this->_getLinkTypes(str_replace(SLI_Search_Model_System_Config_Source_Attributes::LINKED_PRODUCTS_PREFIX . '_', '', $attribute))){
                    $productLinkedData .= $this->_getLinkedProductsXml($attribute, $value);
                } else {
                    $xml .= $this->_getFormattedProductXml($attribute, $value, $productId);
                }
            }
        }

        $xml .= "<status>1</status>";
        $xml .= $this->_getProductCategoriesXml($productId);


        $children = $this->_getProductSubProducts($productId);
        if ($children) {
            $xml .= "<children_ids>$children</children_ids>";
        }



        if(strlen($productLinkedData) > 0) {
                $xml .= "<linked_products>$productLinkedData</linked_products>";
        }

        $xml .= "</product>\n";

        return $xml;
    }

    /**
     * Zend_Db_Select does not allow for some of the data we need with a single query.
     * We will insert dummy data in the array for now and run seperate queries later
     * to get the correct data.
     *
     * @param array $data
     * @return array
     */
    protected function _setDummyData($data) {
        foreach ($this->_seperateQueryAttributes as $separate) {
            $data[$separate] = '';
        }

        return $data;
    }

    /**
     * Convert the attribute and value into the correctly encoded XML format
     *
     * @param $attribute
     * @param $value
     * @return string
     */
    protected function _getFormattedProductXml($attribute, $value, $productId) {

        // Some values cannot be accessed directly with our original query, we need to get them directly
        switch ($attribute) {
            case 'visibility':
                $options = Mage_Catalog_Model_Product_Visibility::getOptionArray();            
                $value = isset($options[$value]) ? $options[$value] : '';
                break;
            case 'parent_id':
                // TODO: Move this into the _addAttributeToProductCollection() using a filtered GROUP_CONCAT within a LEFT JOIN
                $value = array();
                $select = " select product_relation.parent_id from {$this->_getTable("catalog/product_relation")} as product_relation where child_id = {$productId}";
                $parents = $this->_getConnection()->query($select)->fetchAll(PDO::FETCH_ASSOC);

                foreach ($parents as $parent) {
                    $value[] = $parent['parent_id'];
                }

                $value = implode(',', $value);

                // we want to render parent_id as parent_ids per client request
                $attribute = 'parent_ids';
                break;
            case 'has_options':
                if ($value > 0) {
                    $select = "SELECT child_id FROM {$this->_getTable("catalog/product_relation")} as product_relation where parent_id = {$productId}";
                    $children = $this->_getConnection()->query($select)->fetchAll((PDO::FETCH_ASSOC));
                    $value = count($children);
                }
                break;
            default:
                break;
        }

        // this stuff needs to be in this order - before we do anything it needs to be utf-8 encoded - before it was assuming that
        // then other transformations can happen like removing control chars and making it valid for XML
        // we may need to consider doing this in other places in the code where we are using utf8_encode & htmlspecialchars
        $value = utf8_encode($value);
        $value = preg_replace("/[[:cntrl:]]/", " ", $value);
        $value = htmlspecialchars($value, ENT_QUOTES | 8, 'UTF-8'); //ENT_SUBSTITUTE won't work < PHP 5.4, so we use the wrapper value of 8

        return "<$attribute>$value</$attribute>";
    }

    /**
     * Given a product's id, return the category heirarchy structure that it
     * belongs to in an xml string
     *
     * @param string | int $id
     * @return string
     */
    protected function _getProductCategoriesXml($id) {
        $xml = "<categories/>";

        $select = "select cce.path as path from {$this->_getTable("catalog/category_product")} as ccp
            left join {$this->_getTable("catalog/category")} as cce on cce.entity_id = ccp.category_id
            where product_id = $id";

        $paths = $this->_getConnection()->query($select);

        //$this->_log("Gathering category information for product {$id}:  " . $select, 3);

        if (!$paths) {
            return $xml;
        }

        $paths = $paths->fetchAll(PDO::FETCH_ASSOC);
        $paths = $this->_addStoreFilterToPaths($paths);

        if (empty($paths)) {
            return $xml;
        }

        $xml = "";
        $paths = $this->_convertCategoryPaths($paths);
        $xml = $this->_buildCategoryXmlTree($paths, 0, $xml);
        $xml = ($xml) ? "<categories>$xml</categories>" : "<categories/>";

        return $xml;
    }

    /**
     * Check the returned paths and remove the paths that are not within the current store id
     *
     * @param array $paths
     * @return array
     */
    protected function _addStoreFilterToPaths($paths) {

        $query = "SELECT root_category_id FROM {$this->_getTable("core/store_group")} WHERE default_store_id = {$this->_getStoreId()}";
        $categoryId = $this->_getConnection()->query($query);
        $categoryId = $categoryId->fetch();

        if (!$categoryId) {
            $this->_log("Unable to determine root category id, bypassing store filter.", 2);
            return $paths;
        }

        foreach ($paths as $key => $path) {
            $pathSet = explode('/', $path['path']);
            if (!isset($pathSet[0]) || !isset($pathSet[1]) || $pathSet[0] . '/' . $pathSet[1] != "1/" . $categoryId['root_category_id']) {
                unset($paths[$key]);
            } else {
                $e = "Good";
            }
        }

        return $paths;
    }

    /**
     * Convert the string paths to an array of nested paths
     *
     * @param array $paths
     * @return array
     */
    protected function _convertCategoryPaths($paths) {

        $formattedPaths = array();
        foreach ($paths as $path) {
            $path = explode('/', $path['path']);
            array_shift($path); //To remove default category
            array_shift($path); //To remove root category
            $path = array_reverse($path);
            $traverse = array();
            foreach ($path as $category) {
                // We must prefix the key with a string because array_merge_recursive does not preserve numeric keys
                $traverse = array('_' . $category => $traverse);
            }
            $formattedPaths[] = $traverse;
        }

        $merged = array();
        if (count($formattedPaths > 1)) {
            foreach ($formattedPaths as $path) {
                $merged = array_merge_recursive($merged, $path);
            }
        }

        return $merged;
    }

    /**
     * Recursively walks down the paths array and converts each node into an xml string
     *
     * @param array $paths
     * @param string $id
     * @param string $xml
     * @return string
     */
    protected function _buildCategoryXmlTree($paths, $id = null, $xml) {

        $name = '';
        foreach ($paths as $id => $path) {
            $category = ltrim($id, '_');
            $omit = false;
            if (isset($this->_loadedCategoryCache[$category])) {
                $name = $this->_loadedCategoryCache[$category];
            } else {
                if (Mage::helper('sli_search')->isIncludeDisabledCategories($this->_currStoreId)) {
                    $query = "select ccev.value as name from {$this->_getTable("catalog_category_entity_varchar")} as ccev
                        where entity_id = $category and attribute_id = {$this->_getCategoryNameAttributeId()}";

                    $name = current($this->_getConnection()->query($query)->fetchAll(PDO::FETCH_COLUMN));
                    $this->_loadedCategoryCache[$category] = $name;
                } else {
                    $query = "select ccev.value as name, ccei.value as enabled, ccei.store_id as store from {$this->_getTable("catalog_category_entity_varchar")} as ccev, 
                        {$this->_getTable("catalog_category_entity_int")} as ccei
                        where ccev.entity_id = $category and ccev.attribute_id = {$this->_getCategoryNameAttributeId()}
                        and ccei.entity_id = ccev.entity_id and ccei.attribute_id = {$this->_getCategoryEnabledAttributeId()}";

                    $results = $this->_getConnection()->query($query)->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($results as $k => $result) {
                        if ($result['enabled'] == 0 && ($result['store'] == 0 || $result['store'] == $this->_currStoreId)) {
                            $omit = true;
                            unset($this->_loadedCategoryCache[$category]);
                            break;
                        }
                        $name = $result['name'];
                        $this->_loadedCategoryCache[$category] = $name;
                    }
                }
            }

            $name = ($name) ? utf8_encode(htmlspecialchars($name, ENT_QUOTES)) : '';
            $category = ($category) ? utf8_encode(htmlspecialchars($category, ENT_QUOTES)) : '';

            if (!$omit) {
                $xml .= "<category name='$name' id='$category'>";
                if (is_array($path) and count($path)) {
                    $xml = $this->_buildCategoryXmlTree($path, $id, $xml);
                }
                $xml .= "</category>";
            }
        }

        return $xml;
    }

    /**
     * Returns the id of the category 'name' entity attribute
     *
     * @return string | int
     */
    protected function _getCategoryNameAttributeId() {
        if ($this->_categoryNameAttribute === null) {
            $this->_categoryNameAttribute = Mage::getModel('eav/entity')->setType('catalog_category')->getAttribute('name')->getId();
        }
        return $this->_categoryNameAttribute;
    }

    /**
     * Returns the id of the category 'enabled' entity attribute
     *
     * @return string | int
     */
    protected function _getCategoryEnabledAttributeId() {
        return Mage::getModel('eav/entity')->setType('catalog_category')->getAttribute('is_active')->getId();
    }

    /**
     * Given a product's id, returns any associated sub products of the product
     * as a comma delimited string of ids
     *
     * @param string $id
     * @return string
     */
    protected function _getProductSubProducts($id) {
        $select = "select child_id from {$this->_getTable("catalog/product_relation")} where parent_id = $id";
        return $this->_getColumnsFromSelect($select);
    }

    protected function _getLinkedProductsXml($attribute, $value) {
        $xml = '';
        $type = $this->_getLinkTypes(str_replace(SLI_Search_Model_System_Config_Source_Attributes::LINKED_PRODUCTS_PREFIX . '_','',$attribute));
        if($type && strlen($value) > 0){
            $xml .= "<type id=\"$type\">$value</type>";
        }
        return $xml;
    }

    protected function _getLinkTypes($type){
        $productTypes = array(
            'related' => Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED,
            'crosssell' => Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL,
            'upsell' => Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL
        );
        if(array_key_exists($type, $productTypes)){
            return $productTypes[$type];
        }
        return false;
    }

    protected function _getColumnsFromSelect($select) {
        $ids = $this->_getConnection()->query($select);
        if (!$ids) {
            return null;
        }

        $ids = $ids->fetchAll(PDO::FETCH_COLUMN);
        if (empty($ids)) {
            return null;
        }

        return implode(',', $ids);
    }

    /**
     * Back ups two previous feeds.
     *
     * Back up previous feed into backup directory, previous backup
     * into archive, and remove the last archive.
     *
     * @param bool $price_feed
     */
    protected function _backUpFeeds($price_feed = false) {
        $this->_log("Backing up previous feed...", 2);
        $file = $this->_getFeedFileName($price_feed);
        $backupTwo = $this->_getBackupArchivePath();
        $backupOne = $this->_getBackupPath();
        $feeds = $this->_getFeedFilePath();

        if (is_file($backupTwo . DS . $file)) {
            unlink($backupTwo . DS . $file);
        }
        if (is_file($backupOne . DS . $file)) {
            rename($backupOne . DS . $file, $backupTwo . DS . $file);
        }
        if (is_file($feeds . DS . $file)) {
            copy($feeds . DS . $file, $backupOne . DS . $file);
        }
        $this->_log("--Feed Backed Up--", 2);
    }

    /**
     * Returns array of attributes set for the feed from the system configuration
     * Array is formatted as:
     * 0 => $attributeCode
     * 1 => $attributeCode
     * etc..
     *
     * @return array
     */
    protected function _getAttributes() {
        if ($this->_attributes === null) {
            //$this->_attributes = array("name", "url_path");
            $attributes = Mage::helper('sli_search')->getAttributes();
            $unique_attributes = array();
            foreach ($attributes as $attributeLine) {
                if (isset($attributeLine['attribute']) && !in_array($attributeLine['attribute'], $unique_attributes)) {
                    $item = $attributeLine['attribute'];
                    $this->_attributes[] = $item;
                    $unique_attributes[] = $item;
                }
            }
        }
        return $this->_attributes;
    }

    /**
     * Return the set storeId or defaults to default store
     * @return int|string
     * @throws Mage_Core_Exception
     */
    protected function _getStoreId() {
        if ($this->_storeId === null) {
            $this->_storeId = $this->getData('store_id');

            if ($this->_storeId == null) {
                $this->_storeId = Mage::app()->getRequest()->getParam('storeId', null);
            }
            if ($this->_storeId == null) {
                $this->_storeId = Mage::app()->getDefaultStoreView()->getId();
            }
            if (!Mage::getModel('core/store')->load($this->_storeId)->getId()) {
                throw new Mage_Core_Exception("Store for id {$this->_storeId} does not exist");
            }
            $this->_websiteId = Mage::getModel('core/store')->load($this->_storeId)->getWebsiteId();
        }
        return $this->_storeId;
    }

    /**
     * Return the set storeName or defaults to default store
     *
     * @return string | int
     */
    protected function _getStoreName($storeId) {
        return Mage::getModel('core/store')->load($storeId)->getName();
    }

    /**
     * Returns the feed file name based on store
     *
     * @param bool $price_feed
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    protected function _getFeedFileName($price_feed = false) {
        if ($this->_feedFileName === null) {
            if ($price_feed)
                $this->_feedFileName = "sliPriceFeed_{$this->_getStoreId()}.xml";
            else
                $this->_feedFileName = "sliProductFeed_{$this->_getStoreId()}.xml";
        }
        return $this->_feedFileName;
    }

    /**
     * Returns the feed file path
     *
     * @return string
     */
    protected function _getFeedFilePath() {
        return Mage::helper('sli_search/feed')->getFeedFilePath();
    }

    /**
     * If need be, opens socket to feed file and writes data to it.
     *
     * @param string $data
     * @param bool   $price_feed
     */
    protected function _writeToFeedFile($data, $price_feed = false) { 
        if ($this->_feedFileHandle === null) {

            $this->_feedFileHandle = fopen($this->_getFeedFilePath() . DS . $this->_getFeedFileName($price_feed), 'w');
        }

        fputs($this->_feedFileHandle, $data . "\n");
    }

    /**
     * Close out the feed file socket
     */
    protected function _closeFeedFileHandle() {
        if ($this->_feedFileHandle !== null) {
            fclose($this->_feedFileHandle);
        }
    }

    /**
     * Returns the total number of products in the store catalog
     *
     * @return int
     */
    protected function _getProductCount() {
        if ($this->_totalProductCount === null) {
            $count = $this->_getConnection()->query("select count(entity_id) from {$this->_getTable("catalog/product")}");
            $this->_totalProductCount = ($count) ? $count->fetch(PDO::FETCH_COLUMN) : 0;
        }
        return $this->_totalProductCount;
    }

    /**
     * Returns the database connection used by the feed
     *
     * @return PDO
     */
    protected function _getConnection() {
        if (!$this->_dbConnection) {
            $connection = Mage::getConfig()->getNode('global/resources/default_setup/connection');
            $name = $connection->dbname;
            $host = $connection->host;
            $user = $connection->username;
            $pass = $connection->password;

            $this->_dbConnection = new PDO("mysql:dbname=$name;host=$host", $user, $pass);
        }
        $this->_dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        return $this->_dbConnection;
    }

    /**
     * Sends the feed file using ftp to system configured location
     *
     * @param bool $price_feed
     *
     * @return bool
     * @throws SLI_Search_Exception
     */
    protected function _sendFeedFile($price_feed = false) {
        try {
            $this->_log("Sending Feed...", 2);
            $transport = new Varien_Io_Ftp();
            $transport->open($this->_getFtpCredentials());
            $pathToFeedFile = $this->_getFeedFilePath() . DS . $this->_getFeedFileName($price_feed);
            $result = $transport->write($this->_getFeedFileName($price_feed), $pathToFeedFile);
            if($result) {
                $this->_log("--Feed Sent--", 1);
            } else {
                $this->_log("--Feed Failed to Uploaded to FTP--", 1);
                throw new SLI_Search_Exception("Feed Failed to Uploaded to FTP.}");
            }
        } catch (Varien_Io_Exception $e) {
            Mage::logException($e);
            $this->_log("Feed could not be sent via FTP. {$e->getMessage()}", 1);
            throw new SLI_Search_Exception("Feed could not be sent via FTP. {$e->getMessage()}");
        }
    }

    /**
     * - host        required
     * - port        default 21
     * - timeout     default 90
     * - user        default anonymous
     * - password    default empty
     * - ssl         default false
     * - passive     default false
     * - path        default empty
     * - file_mode   default FTP_BINARY
     *
     * @return array
     */
    protected function _getFtpCredentials() {
        if($this->_getData('store_id')){
            return array(
                "host" => Mage::getStoreConfig('sli_search/ftp/host', $this->_getData('store_id')),
                "user" => Mage::getStoreConfig('sli_search/ftp/user', $this->_getData('store_id')),
                "password" => Mage::getStoreConfig('sli_search/ftp/pass', $this->_getData('store_id')),
                "path" => Mage::getStoreConfig('sli_search/ftp/path', $this->_getData('store_id')),
                "passive" => 'true',
            );
        }
        else{
            $helper = Mage::helper('sli_search');
            return array(
                "host" => $helper->getFtpHost(),
                "user" => $helper->getFtpUser(),
                "password" => $helper->getFtpPass(),
                "path" => $helper->getFtpPath(),
                "passive" => 'true',
            );
        }
    }

    /**
     * Create path within var folder if necessary given an array
     * of directory names
     *
     * @param array $directories
     * @return string
     */
    protected function _makeVarPath($directories) {
        return Mage::helper('sli_search/feed')->makeVarPath($directories);
    }

    /**
     * Returns the file uri to the lock file.
     *
     * @param bool $price_feed
     *
     * @return string
     */
    protected function _getFeedLockFile($price_feed = false) {
        if ($this->_feedLockFile === null) {
            $feedDir = $this->_getFeedFilePath();
            $this->_feedLockFile = $feedDir . DS . "{$this->_getFeedFileName($price_feed)}.lock";
        }
        return $this->_feedLockFile;
    }

    /**
     * Writes a lock file for this feed so generation for this feed doesnt
     * happen concurrently.
     *
     * @param bool $price_feed
     */
    protected function _lockFeedGeneration($price_feed = false) {
        file_put_contents($this->_getFeedLockFile($price_feed), 1);
    }

    /**
     * Removes the lock file for this feed
     *
     * @param bool $price_feed
     */
    protected function _unlockFeedGeneration($price_feed = false) {
        $lockFile = $this->_getFeedLockFile($price_feed);
        if (is_file($lockFile)) {
            if (!$price_feed)
                $this->_cleanUpTables();
            unlink($lockFile);
        }
    }

    /**
     * Check to see if the feed is currently locked and cannot be generated.
     * Removes the lock file if older than 1 day
     *
     * @param bool $price_feed
     * @return bool
     */
    protected function _feedIsLocked($price_feed = false) {
        $this->_log('CRON: Checking if feed is locked', 2);
        $lock_file_exists = file_exists($this->_getFeedLockFile($price_feed));
        if($lock_file_exists) {
            $this->_log('CRON: Lock file exists', 2);
            $modification_time = filemtime($lock_file_exists);
            if(!$modification_time) {
                $this->_log('Cron: Unable to get modification time', 2);
            }else {
                if((time() - $modification_time) > (24 * 60 * 60)) { // Check if older than 1 day
                    $this->_unlockFeedGeneration($price_feed = false); // Lock file older than a day so remove it
                    $this->_log('CRON: Lock file old and removed', 2);
                }
            }
        }
        return file_exists($this->_getFeedLockFile($price_feed));
    }

    /**
     * Checks the write permissions on the feed files if they exist and
     * the permissions on the folder that they are located in
     *
     * @param bool $price_feed
     *
     * @throws SLI_Search_Exception
     */
    protected function _checkWritePermissions($price_feed = false){
    	$file_location = $this->_getFeedFilePath() . DS . $this->_getFeedFileName($price_feed);
        $folder_location = $this->_getFeedFilePath();
    	
    	$this->_log("SLI_feed_location: " . $file_location , 2);
    	$this->_log("SLI_folder_location: " . $folder_location , 2);
    	$this->_log("SLI_feed_file_name: " . $this->_getFeedFileName($price_feed), 2);

    	if(file_exists($file_location)){
            $this->_log("Feed file exists: " . $file_location, 2);
            if(!is_writable($file_location)) {
    			$this->_log("Unable to write to file: " . $file_location, 1);
    			throw new SLI_Search_Exception("Unable to write to file: " . $file_location);
        	}
        }else if(!(is_writable($folder_location))) {
    		$this->_log("Unable to write to feed location: " . $folder_location, 1);
    		throw new SLI_Search_Exception("Unable to write to feed location" . $folder_location);
    	}
    }

    /**
     * Remove tables
     *
     * @return type
     */
    protected function _cleanUpTables() {
        $viewResult = $this->_getConnection()->query("SHOW TABLES like 'sliview{$this->_getStoreId()}_%'");
        if (!$viewResult)
            return;
        $viewResultData = $viewResult->fetchAll();

        foreach ($viewResultData as $view) {
            $this->_getConnection()->query("DROP TABLE {$view[0]}");
        }
    }

    /**
     * Get magento's table from the entity resource table uri
     *
     * @param string $tableName
     * @return string
     */
    protected function _getTable($tableName) {
        if (!isset($this->_tableNames[$tableName])) {
            $this->_tableNames[$tableName] = Mage::getSingleton('core/resource')->getTableName($tableName);
        }
        return $this->_tableNames[$tableName];
    }

    /**
     * Returns path to backup directory
     *
     * @return string
     */
    protected function _getBackupPath() {
        if ($this->_feedBackupPath === null) {
            $this->_feedBackupPath = $this->_makeVarPath(array('sli', 'backups', 'bak'));
        }
        return $this->_feedBackupPath;
    }

    /**
     * Returns path to archive directory (second backup)
     *
     * @return string
     */
    protected function _getBackupArchivePath() {
        if ($this->_feedArchivePath === null) {
            $this->_feedArchivePath = $this->_makeVarPath(array('sli', 'backups', 'bak.bak'));
        }
        return $this->_feedArchivePath;
    }

    /**
     * Logs the mysql settings and the php setting vlaues
     */
    protected function _configurationSettings() {
    	//Mysql settings
    	$query = "SHOW VARIABLES where Variable_name = 'max_allowed_packet' OR Variable_name = 'wait_timeout' OR Variable_name = 'connect_timeout' ". 
    	"OR Variable_name = 'innodb_buffer_pool_size'";
    	$result = $this->_getConnection()->query($query);
    	if(!$result) {
            $this->_log("SQL settings query failed", 2);
		}
    	$results = $result->fetchAll(PDO::FETCH_ASSOC);
    	$this->_log("SQL Settings", 3);
    	foreach ($results as $value) {
            $this->_log($value['Variable_name'] . ": " . $value['Value'], 3);
        }
        //Php settings
        $this->_log("PHP Settings", 3);
        $this->_log("Php_memory_limit: " . ini_get("memory_limit"), 3);
    	$this->_log("Php_max_execution_time: " . ini_get('max_execution_time'), 3);
    }


    /**
     * Logs a message to the store log file with current execution time and memory usage
     *
     * Log level is an integer that specifies the type of logging message.
     * 1 is Error, higher levels are for debugging and tracing
     *
     * @param string $msg
     * @param int $loglevel
     * @throws Mage_Core_Exception
     */
    protected function _log($msg, $loglevel = 3) {
        $sli_loglevel = Mage::helper('sli_search')->getLogLevel($this->_currStoreId);
        if ($loglevel && $sli_loglevel && $loglevel > $sli_loglevel)
            return;
        if ($this->_logFile === null) {
            $this->_makeVarPath(array('log', 'sli'));
            $this->_logFile = "sli" . DS . "sliFeedGen_{$this->_getStoreId()}.log";
        }
        $memoryUsage = memory_get_usage(true);
        $memoryUsageFormatted = $memoryUsage / 1024 / 1024 . "M";
        $percentage = "";
        $warningMessage = "";
        $memoryLimit = $this->_memoryLimit;
        if($memoryLimit != "-1") {
            $percentage = sprintf("%.0f", (($memoryUsage / $memoryLimit) * 100)) . "%";
            $warningMessage = $this->_checkMemoryUsage($memoryUsage);
        }
        $time = sprintf("%.4fs", Mage::helper('sli_search')->mtime() - $this->_startTime);
        Mage::log("$time : $memoryUsageFormatted : $percentage $warningMessage-=- $msg", null, $this->_logFile, $this->_isLog);
    }
    
    public function getAjaxNotice() {
        return $this->_ajaxNotice;
    }
    
    /**
     * Setup the max php memory limit
     */
    protected function _setupMemoryLimits() {
        $this->_memoryLimit = ini_get('memory_limit');
        if(substr($this->_memoryLimit, -1) == 'K') {
            $this->_memoryLimit = str_replace('K', '' , $this->_memoryLimit) * 1024 ;
        }else if(substr($this->_memoryLimit, -1) == 'M') {
            $this->_memoryLimit = str_replace('M', '' , $this->_memoryLimit) * 1024 * 1024;
        }else if(substr($this->_memoryLimit, -1) == 'G') {
            $this->_memoryLimit = str_replace('G', '' , $this->_memoryLimit) * 1024 * 1024 * 1024;
        }
        $this->_warningMemoryLimit = $this->_memoryLimit * (self::WARNING_MEMORY_LIMIT / 100);
        $this->_errorMemoryLimit = $this->_memoryLimit * (self::ERROR_MEMORY_LIMIT / 100);
    }

    /**
     * Function to check what the memory usage is currently and how it is compared
     * to the set limits
     *
     * @param $currentUsage
     *
     * @return string
     */
    protected function _checkMemoryUsage($currentUsage) {
        $message = '';
        if( $currentUsage > $this->_warningMemoryLimit) {
            $message = "Warning - Using over ".self::WARNING_MEMORY_LIMIT ."% of php memory";
            if($currentUsage > $this->_errorMemoryLimit) {
                $message = "Error - Using over ".self::ERROR_MEMORY_LIMIT ."% of php memory: ";
            }
        }
        return $message;
    }
}
