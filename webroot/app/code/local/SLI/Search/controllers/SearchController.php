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
     * Feed generation action, linked to from the admin UI
     */
    public function runFeedGenerationAction() {
        $response = array();

        /** @var $sliFeedHelper SLI_Search_Helper_Feed */
        $sliFeedHelper = Mage::helper('sli_search/feed');
        /** @var $sliHelper SLI_Search_Helper_Data */
        $sliHelper = Mage::helper('sli_search');

        try {
            $response = $sliFeedHelper->generateFeedsForAllStores();
        }
        catch (SLI_Search_Exception $e) {
            $response['messages'] = array("Error" => $e->getMessage());
            $response['error'] = true;
        }
        catch (Exception $e) {
            Mage::logException($e);
            $response['messages'] = array("Error" => "An unknown error occurred. Please contact your SLI provider");;
            $response['error'] = true;
        }

        //Email results
        if($sliHelper->sendEmail($response['error'])) {
            Mage::getModel('sli_search/email')
                ->setData('msg', $sliHelper->formatEmailOutput($response['messages']))
                ->setData('subject', 'Manual Feed Generation')
                ->setData('email' , $sliHelper->getFeedEmail())
                ->send();
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
        $error = false;
        try {
            $storeId = $this->getRequest()->getParam("storeId");

            if (!$storeId) {
                $storeId = Mage::app()->getDefaultStoreView()->getId();
            }

            $productFeed = Mage::getModel('sli_search/feed')->setData('store_id', $storeId)->generateFeed();
            $priceFeed = Mage::getModel('sli_search/feed')->setData('store_id', $storeId)->generateFeed(true);
            if($productFeed && $priceFeed) {
                $response = "Feed generated successfully for store {$storeId}";
            }else {
                $response = "Error occurred during feed generation for store {$storeId}";
                $error = true;
            }
        }
        catch (SLI_Search_Exception $e) {
            $response = $e->getMessage();
            $error = true;
        }
        catch (Exception $e) {
            Mage::logException($e);
            $response = "An unknown error occurred. Please contact your SLI provider";
            $error = true;
        }
        $sliHelper = Mage::helper('sli_search');
        if($sliHelper->sendEmail($error)) {
            Mage::getModel('sli_search/email')
                ->setData('msg', $response)
                ->setData('subject', 'Manual Feed Generation')
                ->setData('email', $sliHelper->getFeedEmail())
                ->send();
        }
        $this->getResponse()->setBody($response);
    }
}