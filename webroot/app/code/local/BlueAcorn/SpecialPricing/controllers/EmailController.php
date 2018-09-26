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

        $model = Mage::getModel('blueacorn_specialpricing/token')->load($params['token'], 'token');
        $token = $model->getData();
        $expireDate = $token['token_expiration_date'];
        $currentTime = Mage::getSingleton('core/date')->timestamp();
        $status = $token['status'];
        if("0" == $status || empty($token) || ($expireDate <= $currentTime))
        {
            Mage::getSingleton('core/session')->addError('Token has expired. Please request a new one.');
            $this->_redirect('checkout/cart');
            return;
        }
        $model->setStatus("0")->save();
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

        if (!empty($customerEmail)) {
            $duration = intval(Mage::getStoreConfig('blueacorn_specialpricing/general/token_duration'));
            $map_item = Mage::getModel('blueacorn_specialpricing/token');
            $token = substr(md5(rand()), 0, 10);
            $map_item->setToken($token);
            $map_item->setProductId($productId);
            $map_item->setTokenExpirationDate(strtotime('+' .  $duration . 'hours', Mage::getSingleton('core/date')->timestamp()));
            $map_item->save();

            $email_template = Mage::getModel('core/email_template')->loadByCode('map_request');
            $product = Mage::getModel('catalog/product')->load($productId);
            $addToCartLink = Mage::getBaseUrl() . "map/email/addToCart" . "?product_id=" . $productId . "&token=" . $token;
            $formattedPrice = Mage::helper('core')->formatPrice($product->getFinalPrice(), false);

            $email_variables = array(
                'productName' => $product->getName(),
                'manufacturer' => $product->getAttributeText('manufacturer'),
                'productmpn' => $product->getMpn(),
                'price' => $formattedPrice,
                'link' => $addToCartLink,
                'token' => $token,
                'productImage' => $product->getImageUrl()
            );

            $email_type = Mage::getStoreConfig('blueacorn_specialpricing/general/email_address');
            $sender_name = Mage::getStoreConfig('trans_email/ident_' . $email_type . '/name');
            $sender_email = Mage::getStoreConfig('trans_email/ident_'. $email_type . '/email');
            $email_template->setSenderName($sender_name);
            $email_template->setSenderEmail($sender_email);
            $email_template->addBcc('info@missionrs.com');

            Mage::getSingleton('core/session')->addSuccess('Your email has been sent!');
            $email_template->send($customerEmail, $sender_name, $email_variables);

        } else {
            Mage::getSingleton('core/session')->addError('Please enter a valid email address');
        }

        $parentId = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($productId);

        if(!empty($parentId)) {
            // if there is a parent id redirect to the parent product's url
            $productUrl = Mage::getModel('catalog/product')->load($parentId)->getProductUrl();
        } else {
            // if no parent id redirect to simple product url
            $productUrl = Mage::getModel('catalog/product')->load($productId)->getProductUrl();
        }
        $this->_redirectUrl($productUrl);
    }
}