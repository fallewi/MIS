<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
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
                    if ($params['size']
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
                        $extension = explode(',', $params['extension']);
                        $uploader->setAllowedExtensions($extension);

                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(true);
                        $result = $uploader->save(
                            $uploadDir = Mage::getBaseDir('media') . DS . 'amorderattr' . DS . 'tmp'  . DS
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

    protected function _addFileAttribute($name , $value){
        $session = Mage::getSingleton('checkout/type_onepage')->getCheckout();
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
    }
}
