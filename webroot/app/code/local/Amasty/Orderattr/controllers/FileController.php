<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class Amasty_Orderattr_FileController  extends  Mage_Core_Controller_Front_Action
{
    public function uploadAction()
    {
        $params = Mage::app()->getRequest()->getParams();
        $responce = array();
        /**
         * Uploading files
         */
        if (isset($_FILES['amorderattr']) && isset($_FILES['amorderattr']['error']))
        {
            foreach ($_FILES['amorderattr']['error'] as $attrCode => $errorCode)
            {
                if (UPLOAD_ERR_OK == $errorCode)
                {
                    // check file size
                    if (array_key_exists('size', $params)
                        && $params['size']
                        && ( 1048576 * $params['size']
                            < $_FILES['amorderattr']['size'][$attrCode])
                    ) {
                        $this->_getSession()->addError(
                            $this->__(
                                'File size restriction: %d bytes',
                                1048576 * $params['size']
                            )
                        );
                        continue;
                    }
                    try {
                        if(class_exists("Mage_Core_Model_File_Uploader")){
                            $uploader = new Mage_Core_Model_File_Uploader('amorderattr[' . $attrCode . ']');
                        }
                        else{
                            $uploader = new Varien_File_Uploader('amorderattr[' . $attrCode . ']');
                        }
                        if (array_key_exists('extension', $params)
                            &&  $params['extension']
                        ) {
                            $extension = explode(',', $params['extension']);
                            $uploader->setAllowedExtensions($extension);
                        }

                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(true);
                        if(isset($_FILES['amorderattr']['name'][$attrCode])) {
                            $newName = $_FILES['amorderattr']['name'][$attrCode];
                            $newName = Mage::helper('amorderattr')->normalizeString($newName);
                        }
                        else{
                            $newName = md5(uniqid(rand(), TRUE)) . '.' . $uploader->getFileExtension();
                        }
                        $result = $uploader->save(
                            $uploadDir = Mage::getBaseDir('media') . DS . 'amorderattr' . DS . 'tmp'  . DS,
                            $newName
                        );

                        $this->_addFileAttribute($attrCode, $result['file']);

                        $responce = array(
                            'error'	=> false,
                            'file'	=> $result['file'],
                        );

                    } catch (Exception $e) {
                        $this->_addFileAttribute($attrCode, '');
                        $responce = array(
                            'error' => $e->getMessage(),
                            'errorcode' => $e->getCode());
                    }
                }
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responce));
    }

    public function downloadAction()
    {
        $orderId = $this->getRequest()->getParam('order');
        $order = Mage::getModel('sales/order')->load($orderId);
        if($order && $order->getId()){
            $customerId = $order->getCustomerId();
            if (Mage::getSingleton('customer/session')->isLoggedIn()
                    && $customerId == Mage::getSingleton('customer/session')
                        ->getCustomer()->getId()
            ){
                $this->_download();
                exit();
            }
        }

        $switchSessionName = 'adminhtml';
        $currentSessionId = Mage::getSingleton('core/session')->getSessionId();
        $currentSessionName = Mage::getSingleton('core/session')->getSessionName();
        if ($currentSessionId && $currentSessionName && isset($_COOKIE[$currentSessionName])) {
            $switchSessionId = $_COOKIE[$switchSessionName];
            $this->_switchSession($switchSessionName, $switchSessionId);

            if(Mage::getModel('admin/session')->isLoggedIn()
                && Mage::getSingleton('admin/session')->isAllowed('sales/order')
            ) {
                $this->_download();
            }

            $this->_switchSession($currentSessionName, $currentSessionId);
        }

        exit();
    }

    protected function _switchSession($namespace, $id = null) {
        session_write_close();
        $GLOBALS['_SESSION'] = null;
        $session = Mage::getSingleton('core/session');
        if ($id) {
            $session->setSessionId($id);
        }
        $session->start($namespace);
    }

    protected function _download()
    {
        $attributeCode = $this->getRequest()->getParam('code');
        $orderId = $this->getRequest()->getParam('order');
        $attributeCode = Mage::helper('core')->urlDecode($attributeCode);
        if(!$attributeCode) {
            exit();
        }
        $orderAttributes = Mage::getModel('amorderattr/attribute')->load($orderId, 'order_id');
        if($orderAttributes && $orderAttributes->getId()) {
            $fileName = $orderAttributes->getData($attributeCode);
            if ($fileName) {
                $path = Mage::getBaseDir('media') . DS . 'amorderattr' . DS . 'original' . $fileName;

                if (file_exists($path)) {
                    header(
                        'Content-Disposition: attachment; filename="' . $fileName
                        . '"'
                    );
                    if (function_exists('mime_content_type')) {
                        header(
                            'Content-Type: ' . mime_content_type(
                                $path
                            )
                        );
                    } else if (class_exists('finfo')) {
                        $finfo = new finfo(FILEINFO_MIME);
                        $mimetype = $finfo->file($path);
                        header('Content-Type: ' . $mimetype);
                    }
                    readfile($path);
                }
            }
        }

        exit;
    }

    protected function _addFileAttribute($name , $value, $coreSession = false){
        if ($coreSession) {
            $session = Mage::getSingleton('core/session');
        } else {
            $session = Mage::getSingleton('checkout/type_onepage')->getCheckout();
        }
        $orderAttributes = $session->getAmastyOrderAttributes();
        if (!$orderAttributes)
        {
            $orderAttributes = array();
        }
        if (!Mage::registry('attributeClear')){
            $orderAttributes = array_merge(
                $orderAttributes,
                array(
                    $name  => $value
                )
            );
        }
        $session->setAmastyOrderAttributes($orderAttributes);
        if (!$coreSession) {
            $this->_addFileAttribute($name, $value, true);
        }
    }
}
