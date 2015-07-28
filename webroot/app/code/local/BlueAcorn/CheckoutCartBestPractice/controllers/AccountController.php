<?php
/**
* @package     BlueAcorn\CheckoutCartBestPractice
* @version     0.1.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2015 Blue Acorn, Inc.
*/

require_once(Mage::getModuleDir('controllers', 'Mage_Customer') . DS . 'AccountController.php');

class BlueAcorn_CheckoutCartBestPractice_AccountController extends Mage_Customer_AccountController {


    public function forgotPasswordPostAction()
    {
        if (!$this->getRequest()->isAjax()) {
            return parent::forgotPasswordPostAction();
        }

        $this->_checkForgotPasswordPost();

        $response = $this->getResponse();
        $response->clearAllHeaders();
        $response->setHttpResponseCode(200);

        $messages = Mage::getSingleton('customer/session')->getMessages(true);
        $response->setBody($messages->getLastAddedMessage()->getCode());
        $messages->clear();
    }

    protected function _checkForgotPasswordPost()
    {
        $email = (string) $this->getRequest()->getPost('email');
        if ($email) {
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->_getSession()->setForgottenEmail($email);
                $this->_getSession()->addError($this->__('Invalid email address.'));
                return false;
            }

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken =  $this->_getHelper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                    $this->_getSession()->addError($exception->getMessage());
                    return false;
                }
            }
            $this->_getSession()
                ->addSuccess( $this->_getHelper('customer')
                ->__('If there is an account associated with %s you will receive an email with a link to reset your password.',
                    $this->_getHelper('customer')->escapeHtml($email)));
            return true;
        } else {
            $this->_getSession()->addError($this->__('Please enter your email.'));
            return false;
        }
    }
}