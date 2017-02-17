<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


class Amasty_Orderattr_CustomerController extends Mage_Core_Controller_Front_Action
{
    public function saveAction()
    {
        $orderId = $this->getRequest()->getPost('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$order) {
            $this->_redirectReferer();
            return;
        }
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirectReferer();
            return;
        }
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        if ($customerId != $order->getCustomerId()) {
            $this->_redirectReferer();
            return;
        }

        $data        	 = $this->getRequest()->getPost();
        $orderAttributes = Mage::getModel('amorderattr/attribute');
        $orderAttributes->load($orderId, 'order_id');
        if ($data && isset($data['amorderattr']) && $orderId)
        {
            $data = $data['amorderattr'];
            if (!$orderAttributes->getOrderId())
            {
                $orderAttributes->setOrderId($orderId);
            }
            foreach ($data as $key => $val)
            {
                if ($val) {
                    if (is_array($val)) {
                        $val=implode(', ',$val);
                    }
                }
                $attribute = Mage::getModel('eav/entity_attribute')->load($key, 'attribute_code');
                if ($attribute && 'file' != $attribute->getFrontend()->getInputType()) {
                    $orderAttributes->setData($key, $val);
                }
            }

            /**
             * Deleting
             */
            $toDelete = Mage::app()->getRequest()->getPost('amorderattr_delete');
            if ($toDelete)
            {
                foreach ($toDelete as $attrCode => $value)
                {
                    if ($value == "1") {
                        $url = Mage::getBaseDir('media') . DS . 'amorderattr' . DS . 'original' .
                            $orderAttributes->getData($attrCode);
                        @unlink($url);
                        $orderAttributes->setData($attrCode, '');
                    }
                }
            }

            $files = (array) Mage::getSingleton('core/session')->getAmastyOrderAttributes();
            foreach ($files as $attrCode => $value) {
                if (!$value) {
                    continue;
                }
                $dir = Mage::getBaseDir('media') . DS . 'amorderattr' . DS . 'tmp';
                $file = $dir . $value;
                if ( file_exists($file) && $value ) {
                    try {
                        $newPath = Mage::getBaseDir('media') . DS . 'amorderattr' . DS . 'original' . $value;
                        $pos = strrpos($newPath, "/");
                        if ($pos) {
                            $destinationDir = substr($newPath, 0, $pos);
                            if (!is_dir($destinationDir)) {
                                mkdir($destinationDir, 0755, true);
                            }
                        }
                        $result = rename(
                            $file, $newPath
                        );
                        $orderAttributes->setData($attrCode, $value);
                    }
                    catch(Exception $ex){
                    }
                }
            }

            try {
                Mage::getSingleton('core/session')->setAmastyOrderAttributes(null);
                $orderAttributes->save();
                $order->addStatusHistoryComment(Mage::helper('amorderattr')->__("Custom attribute(s) changed by customer."));
                $order->save();
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('sales')->__('The order attributes have been updated.'));
                $this->_sendEmail($order->getIncrementId());
                $this->_redirectReferer();
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addException(
                    $e,
                    Mage::helper('amorderattr')->__('An error occurred while updating the order attributes.')
                );
            }
        } else {
            $this->_redirectReferer();
        }
    }

    protected function _sendEmail($orderId)
    {
        if (Mage::getStoreConfig('amorderattr/general/notify_admin')) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            $template = Mage::getStoreConfig('amorderattr/general/email_template');
            $recipient = Mage::getStoreConfig('amorderattr/general/email_to');
            $emailModel = Mage::getModel('core/email_template');
            $emailModel->sendTransactional(
                $template,
                'general',
                Mage::getStoreConfig('trans_email/ident_' . $recipient . '/email'),
                Mage::helper('amorderattr')->__('Order custom attributes have been changed'),
                array(
                    'order_id' => $orderId
                )
            );
            $translate->setTranslateInline(true);
        }
        return $this;
    }
}