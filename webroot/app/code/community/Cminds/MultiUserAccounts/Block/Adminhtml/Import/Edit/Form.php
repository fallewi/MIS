<?php

class Cminds_MultiUserAccounts_Block_Adminhtml_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $afterElementHtml = '<p class="nm">
								<small>
									Download <a href="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'multiuser/sample.csv">sample CSV</a>
									<br/><br/>
									<strong>If new customer customer_id field have to be empty:</strong> <br/><br/>

									<strong>Permission:</strong> <br/>
									 - Read Only : 1 <br/>
									 - Modify Account : 2 <br/>
									 - Order Creation : 3 <br/>
									 - Order Creation & Modify Account : 4 <br/>
									 - Read Only/Approval needed : 5 <br/> <br/>

									 Password is set only for new customers / subaccounts.
								</small>
							</p>';

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/savePost'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('cminds_multiuseraccounts')->__('Import')
        ));

        $fieldset->addField('file', 'file',
            array(
                'label' => Mage::helper('cminds_multiuseraccounts')->__('CSV file'),
                'name' => 'file',
                'required' => true,
                'after_element_html' => $afterElementHtml,
            )
        );

        $form->setHtmlIdPrefix('_import');


        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }


}
