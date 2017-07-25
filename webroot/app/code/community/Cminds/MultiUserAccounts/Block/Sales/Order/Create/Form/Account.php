<?php

class Cminds_MultiUserAccounts_Block_Sales_Order_Create_Form_Account extends Mage_Adminhtml_Block_Sales_Order_Create_Form_Account
{
    protected function _prepareForm()
    {
        $customerData = $this->getCustomerData();
        $isParentCustomer = Mage::helper('cminds_multiuseraccounts')->isParentCustomer($customerData['entity_id']);

        /* @var $customerModel Mage_Customer_Model_Customer */
        $customerModel = Mage::getModel('customer/customer');

        /* @var $customerForm Mage_Customer_Model_Form */
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setFormCode('adminhtml_checkout')
            ->setStore($this->getStore())
            ->setEntity($customerModel);

        // prepare customer attributes to show
        $attributes = array();

        // add system required attributes
        foreach ($customerForm->getSystemAttributes() as $attribute) {
            /* @var $attribute Mage_Customer_Model_Attribute */
            if ($attribute->getIsRequired()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        if ($this->getQuote()->getCustomerIsGuest()) {
            unset($attributes['group_id']);
        }

        // add user defined attributes
        foreach ($customerForm->getUserAttributes() as $attribute) {
            /* @var $attribute Mage_Customer_Model_Attribute */
            $attributes[$attribute->getAttributeCode()] = $attribute;
        }

        $fieldset = $this->_form->addFieldset('main', array());
        if ($isParentCustomer) {
            $fieldset->addField('subaccount_id', 'select', array(
                'label' => Mage::helper('cminds_multiuseraccounts')->__('Choose SubAccount'),
                'title' => Mage::helper('cminds_multiuseraccounts')->__('Choose SubAccount'),
                'name' => 'subaccount_id',
                'values' => Mage::getResourceModel('cminds_multiuseraccounts/subAccount_collection')->getSubWithOrderPermission($customerData['entity_id']),
            ));
        }
        $this->_addAttributesToForm($attributes, $fieldset);

        $this->_form->addFieldNameSuffix('order[account]');

        $this->_form->setValues($this->getFormValues());

        return $this;
    }
}