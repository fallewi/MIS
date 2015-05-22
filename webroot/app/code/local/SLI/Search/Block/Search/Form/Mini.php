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
 * Search Mini Form block.
 * Provides rendering abilities for SLI version of the form.mini.phtml that
 * replaces the search url with an external url to an SLI hosted search page.
 * Provides an inline search autocomplete feature as well.
 * 
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Block_Search_Form_Mini extends Mage_Core_Block_Template {

    /**
     * Returns SLI provided auto complete javascript.
     *
     * @return string
     */
    public function getInlineAutocompleteJs() {
        return Mage::helper('sli_search')->getAutocompleteJs();
    }

    /**
     * Returns External search domain to the search page hosted by SLI.
     *
     * @return string
     */
    public function getSearchUrl() {
        $url = Mage::helper('sli_search')->getSearchDomain();
        $scheme = parse_url($url, PHP_URL_SCHEME);

        if (!$scheme) {
            $url = "http://".$url;
        }

        return $url;
    }

    /**
     * Retrieve the form code from the database for this site
     *
     * @return string
     */
    public function getFormData() {
        return $data = Mage::helper('sli_search')->getFormData();
    }

    /**
     * Switch out the default form mini template for the sli version
     *
     * @return string
     */
    protected function _toHtml() {
        if (Mage::helper('sli_search')->isEnabled(Mage::app()->getStore()->getId())) {
            if(Mage::helper('sli_search')->useCustomForm()) {
                return $this->getFormData();
            }else {
                $this->setTemplate('sli/search/form.mini.phtml');
            }
        }
        return parent::_toHtml();
    }
}