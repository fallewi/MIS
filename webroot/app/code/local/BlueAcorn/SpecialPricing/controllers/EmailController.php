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

        $email_template = Mage::getModel('core/email_template')->loadByCode('map_request');
        $product = Mage::getModel('catalog/product')->load($productId);
        $addToCartLink = Mage::getBaseUrl() . "map/email/addToCart" . "?product_id=" . $productId . "&token=" .$token;

        $email_variables = array(
            'productName'   => $product->getName(),
            'manufacturer'  => $product->getManufacturer(),
            'price'     => $product->getPrice(),
            'link'      => $addToCartLink,
            'token'     => $token,
        );

        $sender_name = Mage::getStoreConfig('trans_email/ident_general/name');
        $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
        $email_template->setSenderName($sender_name);
        $email_template->setSenderEmail($sender_email);
        $email_template->setTemplateSubject("Mission Restaurant Supply - Exclusive Price Request");

        $email_template->send($customerEmail, $sender_name, $email_variables);
        $productUrl = Mage::getModel('catalog/product')->load($productId)->getProductUrl();
        $this->_redirectUrl($productUrl);
    }
}