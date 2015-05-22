<?php
/**
 * Controller than provides the frontend capabilities of the SLI Search integration.
 * 
 * @package SLI
 * @subpackage Search
 * @author Blue Acorn: Brys Sepulveda
 */

class SLI_Search_SearchController extends Mage_Core_Controller_Front_Action {
    
    /**
     * Renders a standard frontend page using just the default handles.
     * Nothing is defined in the layout for this page and it should have an empty
     * content.
     *
     * SLI uses this page as a template for their hosted search solution
     */
    public function templateAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Asynchronous posting to feed generation url for each store. 
     */
    public function runFeedGenerationAction() {
        $response = array("error" => false);

        try {
            Mage::helper('sli_search/feed')->generateFeedsForAllStores();
        }
        catch (SLI_Search_Exception $e) {
            $response['error'] = $e->getMessage();
        }
        catch (Exception $e) {
            Mage::logException($e);
            $response['error'] = "An unknown error occurred. Please contact your SLI provider";
        }
        $this->getResponse()
                ->setHeader("Content-Type", "application/json")
                ->setBody(json_encode($response));
    }

    /**
     * Generates a feed based on passed in store id. Defaults store id to 
     * default store 
     */
    public function generateFeedAction() {
        $response = "";
        try {
            $storeId = $this->getRequest()->getParam("storeId");

            if (!$storeId) {
                $storeId = Mage::app()->getDefaultStoreView()->getId();
            }

            Mage::getModel('sli_search/feed')->setData('store_id', $storeId)->generateFeed();
            Mage::getModel('sli_search/feed')->setData('store_id', $storeId)->generateFeed(true);            
        }
        catch (SLI_Search_Exception $e) {
            $response = $e->getMessage();
        }
        catch (Exception $e) {
            Mage::logException($e);
            $response = "An unknown error occurred. Please contact your SLI provider";
            //@TODO Send a magento message of feed failure
        }
        $this->getResponse()->setBody($response);
    }
}