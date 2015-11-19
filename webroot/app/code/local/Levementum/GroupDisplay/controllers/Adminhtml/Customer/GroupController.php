<?php
/**
 * @category    Levementum
 * @package     Levementum_
 * @file        ${FILE_NAME}
 * @auther      jjuleff@levementum.com
 * @date        4/7/14 1:22 PM
 * @brief       
 * @details     
 */
require_once 'Mage/Adminhtml/controllers/Customer/GroupController.php';


class Levementum_GroupDisplay_Adminhtml_Customer_GroupController extends Mage_Adminhtml_Customer_GroupController
{
	public function saveAction()
	{
		$imageField = 'customer_group_image_url';

		if (isset($_FILES[$imageField]['name']) and (file_exists($_FILES[$imageField]['tmp_name'])))
		{
			try{
				$uploader = new Varien_File_Uploader($imageField);
				$uploader->setAllowedExtensions('jpg','jpeg','gif','png');
				$uploader->setAllowRenameFiles(false);
				$uploader->setFilesDispersion(false);
				$path = Mage::getBaseDir('media').DS.'uploaddir'.DS;
				$uploader->save($path,$_FILES[$imageField]['name']);
				$data[$imageField] = 'uploaddir'.DS.$_FILES[$imageField]['name'];

			}
			catch (Exception $e)
			{
				Mage::log($e->getMessage(),null,'uploader.log',true);
			}
		}
		else
		{
			$file = $this->getRequest()->getParam($imageField);
			if (isset($file['delete']) && $file['delete'] == 1)
				$data[$imageField] = '';
		}

		$customerGroup = Mage::getModel('customer/group');
		$id = $this->getRequest()->getParam('id');
		if (!is_null($id)) {
			$customerGroup->load((int)$id);
		}

		$taxClass = (int)$this->getRequest()->getParam('tax_class');

		if ($taxClass) {
			try {
				$customerGroupCode = (string)$this->getRequest()->getParam('code');

				if (!empty($customerGroupCode)) {
					$customerGroup->setCode($customerGroupCode);
				}
				if ( isset( $data[ $imageField ] ) )
				{
					$customerGroup->setCustomerGroupImageUrl ( $data[ $imageField ] );
				}

				$customerGroup->setTaxClassId($taxClass)->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customer')->__('The customer group has been saved.'));
				$this->getResponse()->setRedirect($this->getUrl('*/customer_group'));
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setCustomerGroupData($customerGroup->getData());
				$this->getResponse()->setRedirect($this->getUrl('*/customer_group/edit', array('id' => $id)));
				return;
			}
		} else {
			$this->_forward('new');
		}
	}
}