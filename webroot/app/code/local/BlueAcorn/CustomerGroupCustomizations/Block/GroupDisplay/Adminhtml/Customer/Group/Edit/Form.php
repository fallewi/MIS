<?php
/**
 * @package     BlueAcorn\CustomerGroupCustomizations
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */
class BlueAcorn_CustomerGroupCustomizations_Block_GroupDisplay_Adminhtml_Customer_Group_Edit_Form extends Levementum_GroupDisplay_Block_Adminhtml_Customer_Group_Edit_Form {
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $form = $this->getForm();
        $customerGroup = Mage::registry('current_group');
        $fieldset = $form->getElement('base_fieldset');

        $fieldset->addField('linked_category','select',
            array(
                'name'  => 'linked_category',
                'label' => 'Associated Category for My Account Link',
                'title' => 'Associated Category for My Account Link',
                'required' => false,
                'values' => Mage::helper('blueacorn_customergroupcustomizations')->getCategoriesThatCanBeLinked()
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