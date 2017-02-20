<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class Amasty_Orderattr_Adminhtml_AmorderattrfileController extends Mage_Adminhtml_Controller_Action
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
								&& $params['extension']
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

	protected function _addFileAttribute($name , $value, $coreSession = false){
		if ($coreSession) {
			$session = Mage::getSingleton('adminhtml/session');
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

    protected function _isAllowed()
    {
        return true;
    }
}
