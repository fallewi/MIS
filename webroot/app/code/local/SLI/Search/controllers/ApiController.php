<?php
/**
 *
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
 *
 * Provide the URL for SLI to retrieve the current shopping cart items and all relative information
 * An JSON file will be passedback
 *
 */
class SLI_Search_ApiController extends Mage_Core_Controller_Front_Action
{
    //Decare the quote
    private $_quote = NULL;
    
    /**
     * Load the quote by passing the quote id
     * 
     * @param int $quoteId
     * @return Mage_Sales_Model_Quote
     */
   private function _getQuote()
    {
        if( $this->_quote ) return $this->_quote;
        else return Mage::getSingleton('checkout/session')->getQuote();
    }
    
    /**
     * Allow SLI to call this url to get the cart jsonp object
     * 
     */
    public function cartAction()
    {
        $jsonpResult = Mage::helper( 'sli_search' )->getCartJSONP( $this->_getQuote() );
        
        if( $jsonpResult )
            $this->getResponse()->setBody( $jsonpResult );
    }



}