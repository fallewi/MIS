<?php

/**
 * Created by PhpStorm.
 * User: forrest
 * Date: 8/11/15
 * Time: 6:37 PM
 */
class BlueAcorn_SpecialPricing_EmailController extends Mage_Core_Controller_Front_Action
{
    public function addToCartAction()
    {
        $params = $this->getRequest()->getParams();

        $product = Mage::getModel('catalog/product')->load($params['product_id']);
        $cart = Mage::getModel('checkout/cart');
        $cart->init();
        $cart->addProduct($product);
        $cart->save();
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        $this->_redirect('checkout/cart');

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        foreach($quote->getAllVisibleItems() as $item)
        {
            if($item->getProductId() == $params['product_id'])
            {
                $map_item = Mage::getModel('blueacorn_specialpricing/token')->load($params['token'], 'token');
                $map_item->setQuoteItemId($item->getQuoteId());
                $map_item->save();
            }
        }
    }

    public function requestTokenAction()
    {
        $params = $this->getRequest()->getParams();
        $productId = $params['product_id'];
        $customerEmail = $params['email'];

        $duration = intval(Mage::getStoreConfig('blueacorn_specialpricing/general/token_duration'));
        $map_item = Mage::getModel('blueacorn_specialpricing/token');
        $token = substr(md5(rand()),0,10);
        $map_item->setToken($token);
        $map_item->setProductId($productId);
        $map_item->setTokenExpirationDate(time() + $duration * 60 * 60);
        $map_item->save();

        $email_template = Mage::getModel('core/email_template')->loadDefault('map_request');
        $product = Mage::getModel('catalog/product')->load($productId);
        $addToCartLink = Mage::getBaseUrl() . "map/email/addToCart" . "?product_id=" . $productId . "&token=" .$token;

        $email_variables = array(
            'product'   => $product->getName(),
            'price'     => $product->getPrice(),
            'link'      => $addToCartLink,
            'token'     => $token,
        );

        $sender_name = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME);
        $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
        $email_template->setTemplateSenderName($sender_name);
        $email_template->setTemplateSenderEmail($sender_email);

        $email_template->send($customerEmail, null, $email_variables);
        $productUrl = Mage::getModel('catalog/product')->load($productId)->getProductUrl();
        $this->_redirectUrl($productUrl);
    }
}