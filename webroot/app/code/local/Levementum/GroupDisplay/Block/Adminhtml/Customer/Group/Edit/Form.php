<?php
/**
 * @category    Levementum
 * @package     Levementum_
 * @file        ${FILE_NAME}
 * @auther      jjuleff@levementum.com
 * @date        4/7/14 11:54 AM
 * @brief       
 * @details     
 */ 
class Levementum_GroupDisplay_Block_Adminhtml_Customer_Group_Edit_Form extends Mage_Adminhtml_Block_Customer_Group_Edit_Form {
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$form = $this->getForm();
		$form->setData('enctype','multipart/form-data');
		$form->setData('method','post');

		$customerGroup = Mage::registry('current_group');
		$fieldset = $form->getElement('base_fieldset');

		$fieldset->addField('customer_group_image_url','image',
		                    array(
			                    'name'  => 'customer_group_image_url',
			                    'label' => Mage::helper('customer')->__('Customer Group Image'),
			                    'title' => Mage::helper('customer')->__('Customer Group Image'),
			                    'required' => false,
								'index' => 'customer_group_image_url'
		                    )
		);


		if( Mage::getSingleton('adminhtml/session')->getCustomerGroupData() ) {
			$form->addValues(Mage::getSingleton('adminhtml/session')->getCustomerGroupData());
			Mage::getSingleton('adminhtml/session')->setCustomerGroupData(null);
		} else {
			$form->addValues($customerGroup->getData());
		}

		$this->setForm($form);
	}
}