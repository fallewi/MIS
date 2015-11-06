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
 *
 * @package SLI
 * @subpackage Search
 */

require_once('../abstract.php');

class SLI_Search_Shell_Feed extends Mage_Shell_Abstract {

    /**
     *
     *
     * @return boolean
     */
    public function run() {
        if ($this->getArg('build')) {
            try {
                $id = $this->getArg('store');
                if (!is_bool($id)) {
                    Mage::getModel('sli_search/feed')->setData('store_id', $id)->generateFeed();     //Standard feed
                    Mage::getModel('sli_search/feed')->setData('store_id', $id)->generateFeed(true); //Price feed
                    echo "Feed generated for store $id.\n";
                    return true;
                }
                else if (count($this->_args) == 1){
                    Mage::helper('sli_search/feed')->generateFeedsForAllStores();
                    echo "Generating feeds for all stores.\n";
                    return true;
                }
            }
            catch (SLI_Search_Exception $e) {
                echo "{$e->getMessage()}\n";
                return false;
            }
            catch (Exception $e) {
                Mage::logException($e);
                echo "An unknown error occurred. Please contact your SLI provider. {$e->getMessage()}\n";
                return false;
            }
        }
        echo $this->usageHelp();
        return false;
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f feed.php -- [options]

  build                     Builds Feeds For All Stores
  build --store [#]         Builds Feed For Specific Store

USAGE;
    }

}

$sliSearchFeed = new SLI_Search_Shell_Feed();
$sliSearchFeed->run();