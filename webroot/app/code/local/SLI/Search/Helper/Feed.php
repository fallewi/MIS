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
 * Feed helper for multifunctional feed related utilities
 * 
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Helper_Feed {
    
    protected $_feedFilePath = null;
    
    /**
     * Open socket to feed generation url with store id as passed parameter.
     *
     *
     * @deprecated
     * @param Mage_Core_Model_Store $store
     * @param array $urlParts
     * @throws Mage_Core_Exception
     * @throws SLI_Search_Exception
     */
    public function postToGenerateFeed($store, $urlParts) {
        $feedSocket = @fsockopen($urlParts['host'], 80, $errNo, $errStr, 10);
                
        if (!$feedSocket) {
            throw new SLI_Search_Exception("Err. #$errNo: Cannot access feed generation uri.");
        }

        $storeParam = "storeId={$store->getId()}";
        $storeParamLen = strlen($storeParam);

        $EOL = "\r\n";
        $request = "POST {$urlParts['path']} HTTP/1.1$EOL";
        $request .= "HOST: {$urlParts['host']}$EOL";
        $request .= "Content-Length: $storeParamLen$EOL";
        $request .= "Content-Type: application/x-www-form-urlencoded$EOL";
        $request .= "Connection: Close$EOL$EOL";
        $request .= "$storeParam";

        $result = fwrite($feedSocket, $request);
        
        if (!$result) {
            throw new SLI_Search_Exception("Error writing to feed generation uri.");
        }
        fclose($feedSocket);
    }
    
    /**
     * Returns url that controls feed generation
     * 
     * @return string
     */
    public function getGenerateFeedUrl() {
        $curStore = Mage::app()->getStore();
        Mage::app()->setCurrentStore(1); //default store number...always 1
        $myUrl = Mage::getUrl('sli_search/search/generateFeed');
        Mage::app()->setCurrentStore($curStore);       
        return $myUrl;
    }

    /**
     * Starts a feed generation for each store
     * @return array
     * @throws SLI_Search_Exception
     */
    public function generateFeedsForAllStores() {
        if ($this->thereAreFeedLocks()) {
            throw new SLI_Search_Exception("One or more feeds are being generated. Generation temporarily locked.");
        }

        $toReturn = array();
        $messages = array();
        $toReturn['error'] = false;

        /** @var $stores Mage_Core_Model_Resource_Store_Collection */
        $stores = Mage::getResourceModel('core/store_collection');

        foreach($stores as $store){
            $storeId = $store->getId();
            try {
                $productFeedStatus = Mage::getModel('sli_search/feed')->setData('store_id', $storeId)->generateFeed();
                if (true === $productFeedStatus) {
                    $messages["Product Feed " . $storeId ] = "Success";
                } else {
                    $messages["Product Feed " . $storeId ] = $productFeedStatus;
                }
                $priceFeedStatus = Mage::getModel('sli_search/feed')->setData('store_id', $storeId)->generateFeed(true);
                if (true === $priceFeedStatus) {
                    $messages["Price Feed " . $storeId] = "Success";
                } else {
                    $messages["Price Feed " . $storeId] = $priceFeedStatus;
                }
            }catch (Exception $e) {
                Mage::logException($e);
                $toReturn['error'] = true;
                $messages["Feed " .$storeId] = "Exception generating feed $storeId -> " . $e->getMessage();
            }
        }
        $toReturn['messages'] = $messages;
        return $toReturn;
    }
    
    /**
     * Returns the feed file path
     * 
     * @return string
     */
    public function getFeedFilePath() {
        if ($this->_feedFilePath === null) {
            $this->_feedFilePath = $this->makeVarPath(array('sli', 'feeds'));
        }
        return $this->_feedFilePath;
    }
    
    /**
     * Create path within var folder if necessary given an array
     * of directory names
     * 
     * @param array $directories
     * @return string 
     */
    public function makeVarPath($directories) {
        $path = Mage::getBaseDir('var');
        foreach ($directories as $dir) {
            $path .= DS . $dir;
            if (!is_dir($path)) {
                @mkdir($path, 0777);
            }
        }
        return $path;
    }
    
    /**
     * Whether or not there are feed generation locks currently in place
     *
     * @return boolean 
     */
    public function thereAreFeedLocks() {
        $path = $this->getFeedFilePath();
        foreach (scandir($path) as $file) {
            $fullFile = $path.DS.$file;
            if (is_file($fullFile) && !is_dir($fullFile) && is_numeric(strpos($file, '.lock'))) {
                $modification_time = filemtime($fullFile);
                if(!$modification_time) {
                    return true;
                }else {
                    if ((time() - filemtime($fullFile)) > (24 * 60 * 60)) { // Check if older than 1 day
                        unlink($fullFile); // Lock file older than a day so remove it
                        return false;
                    }
                    return true;
                }
            }
        }
        return false;
    }
    
}