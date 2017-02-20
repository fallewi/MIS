<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class Amasty_Orderattr_Adminhtml_AmorderattrfiledownloadController extends Mage_Adminhtml_Controller_Action
{
	public function preDispatch()
	{
		$this->_publicActions[] = 'download';
		return parent::preDispatch();
	}

	protected function downloadAction()
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

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order');
    }
}
