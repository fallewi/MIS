<?php

class Cminds_MultiUserAccounts_Block_Adminhtml_SubAccount_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Initialize form
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Account
     */
    protected function _prepareForm()
    {
        $subAccount = Mage::registry('sub_account');
        $data = $subAccount->getData();
        $mode = 'edit';
        if ($subAccount && $subAccount->getId()) {
        } else {
            $mode = 'new';
            $data['parent_customer_id'] = $this->getRequest()->getParam('parent_customer_id');
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('_subaccount');
        $form->setFieldNameSuffix('subaccount');
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('cminds_multiuseraccounts')->__('Account Information')
        ));

        $fieldset->addField('parent_customer_id', 'hidden',
            array(
                'name' => 'parent_customer_id',
                'required' => true,
            )
        );

        // New customer password
        $fieldset->addField('firstname', 'text',
            array(
                'label' => Mage::helper('cminds_multiuseraccounts')->__('First Name'),
                'name' => 'firstname',
                'required' => true,
            )
        );

        $fieldset->addField('lastname', 'text',
            array(
                'label' => Mage::helper('cminds_multiuseraccounts')->__('Last Name'),
                'name' => 'lastname',
                'required' => true
            )
        );

        $fieldset->addField('email', 'text',
            array(
                'label' => Mage::helper('cminds_multiuseraccounts')->__('Email'),
                'name' => 'email',
                'required' => true
            )
        );

        $fieldset->addField('permission', 'select', array(
            'label' => Mage::helper('cminds_multiuseraccounts')->__('Permission'),
            'name' => 'permission',
            'values' => Mage::getModel('cminds_multiuseraccounts/subAccount_permission')->getAllOptions(),
            'required' => true
        ));

        $fieldset->addField('is_approver', 'select', array(
            'label' => Mage::helper('cminds_multiuseraccounts')->__('Can approve shopping carts'),
            'name' => 'is_approver',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')
                ->toOptionArray(),
            'required' => false,
            'note' => 'Allows to Approve Cart of Sub Accounts with Approval needed'
        ));

        $fieldset->addField('assigned_approvers', 'multiselect', array(
            'label' => Mage::helper('cminds_multiuseraccounts')->__('Assigned Approvers'),
            'name' => 'assigned_approvers',
            'values' => $this->getApprovers($subAccount),
            'required' => false,
        ));

        $fieldset->addField('view_all_orders', 'select', array(
            'label' => Mage::helper('cminds_multiuseraccounts')->__('View All Orders'),
            'name' => 'view_all_orders',
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('cminds_multiuseraccounts')->__('No')),
                array('value' => '1', 'label' => Mage::helper('cminds_multiuseraccounts')->__('Yes')),
            ),
            'required' => true
        ));
        $fieldset->addField('can_see_cart', 'select', array(
            'label' => Mage::helper('cminds_multiuseraccounts')->__('Can See to Cart Page'),
            'name' => 'can_see_cart',
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('cminds_multiuseraccounts')->__('No')),
                array('value' => '1', 'label' => Mage::helper('cminds_multiuseraccounts')->__('Yes')),
            ),
            'required' => true
        ));
        $fieldset->addField('have_access_checkout', 'select', array(
            'label' => Mage::helper('cminds_multiuseraccounts')->__('Have Access to Checkout'),
            'name' => 'have_access_checkout',
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('cminds_multiuseraccounts')->__('No')),
                array('value' => '1', 'label' => Mage::helper('cminds_multiuseraccounts')->__('Yes')),
            ),
            'required' => true
        ));
        $fieldset->addField('get_order_email', 'select', array(
            'label' => Mage::helper('cminds_multiuseraccounts')->__('Get copy of order emails'),
            'name' => 'get_order_email',
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('cminds_multiuseraccounts')->__('No')),
                array('value' => '1', 'label' => Mage::helper('cminds_multiuseraccounts')->__('Yes')),
            ),
            'required' => true
        ));

        $fieldset->addField('get_order_invoice', 'select', array(
            'label' => Mage::helper('cminds_multiuseraccounts')->__('Get copy of invoice emails'),
            'name' => 'get_order_invoice',
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('cminds_multiuseraccounts')->__('No')),
                array('value' => '1', 'label' => Mage::helper('cminds_multiuseraccounts')->__('Yes')),
            ),
            'required' => true
        ));

        $fieldset->addField('get_order_shipment', 'select', array(
            'label' => Mage::helper('cminds_multiuseraccounts')->__('Get copy of shipment emails'),
            'name' => 'get_order_shipment',
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('cminds_multiuseraccounts')->__('No')),
                array('value' => '1', 'label' => Mage::helper('cminds_multiuseraccounts')->__('Yes')),
            ),
            'required' => true
        ));

        $fieldset->addField('order_amount_limit', 'select', array(
            'label' => Mage::helper('cminds_multiuseraccounts')->__('Limit Order Amount per:'),
            'name' => 'order_amount_limit',
            'values' => Mage::getModel('cminds_multiuseraccounts/subAccount_limits')->getAllOptions(),
            'required' => true
        ));

        $fieldset->addField('order_amount_limit_value', 'text', array(
                'label' => Mage::helper('cminds_multiuseraccounts')->__('Limit Order Amount Value'),
                'name' => 'order_amount_limit_value',
                'required' => true
        ));

        // Add password management fieldset
        $newFieldset = $form->addFieldset(
            'password_fieldset',
            array('legend' => Mage::helper('cminds_multiuseraccounts')->__('Password Management'))
        );
        // New customer password
        $newFieldset->addField('new_password', 'password',
            array(
                'label' => 'new' == $mode ? Mage::helper('cminds_multiuseraccounts')->__('Password') : Mage::helper('cminds_multiuseraccounts')->__('New Password'),
                'name' => 'new_password',
                'class' => 'validate-new-password'
            )
        );

        if ('new' == $mode) {
            $newFieldset->addField('password_confirmation', 'password',
                array(
                    'label' => Mage::helper('cminds_multiuseraccounts')->__('Confirm Password'),
                    'name' => 'password_confirmation',
                )
            );
        }

//        // Prepare customer confirmation control (only for existing customers)
        $confirmationKey = $subAccount->getConfirmation();
        if ($confirmationKey || $subAccount->isConfirmationRequired()) {
            $fieldset->addField('confirmation', 'select', array(
                'name' => 'confirmation',
                'label' => Mage::helper('cminds_multiuseraccounts')->__('Confirmation'),
                'values' => array(
                    array(
                        'value' => 0,
                        'label' => Mage::helper('cminds_multiuseraccounts')->__('Not Confirmation')
                    ),
                    array(
                        'value' => 1,
                        'label' => Mage::helper('cminds_multiuseraccounts')->__('Confirmation')
                    )
                ),
            ));
        }
        $data['assigned_approvers'] = unserialize($data['assigned_approvers']);
        $form->setValues($data);
        $this->setForm($form);

        $this->setChild('form_after', $this->getLayout()
            ->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap("{$htmlIdPrefix}permission", 'permission')
            ->addFieldMap("{$htmlIdPrefix}is_approver", 'is_approver')
            ->addFieldMap("{$htmlIdPrefix}order_amount_limit", 'order_amount_limit')
            ->addFieldMap("{$htmlIdPrefix}order_amount_limit_value", 'order_amount_limit_value')
            ->addFieldMap("{$htmlIdPrefix}assigned_approvers", 'assigned_approvers')
            ->addFieldDependence(
                'is_approver',
                'permission',
                Cminds_MultiUserAccounts_Model_SubAccount_Permission::PERMISSION_ORDER_WRITE
            )
            ->addFieldDependence(
                'assigned_approvers',
                'permission',
                Cminds_MultiUserAccounts_Model_SubAccount_Permission::PERMISSION_NEED_APPROVAL
            )
            ->addFieldDependence(
                'order_amount_limit_value',
                'order_amount_limit',
                array(
                    (string) Cminds_MultiUserAccounts_Model_SubAccount_Limits::LIMIT_DAY,
                    (string) Cminds_MultiUserAccounts_Model_SubAccount_Limits::LIMIT_MONTH,
                    (string) Cminds_MultiUserAccounts_Model_SubAccount_Limits::LIMIT_YEAR
                )
            )
        );

        return parent::_prepareForm();
    }

    public function getApprovers($subAccount)
    {
        $approvers = array();

        foreach($subAccount->getApprovers() as $approver) {
            $approvers[] =array(
                'value' => $approver->getId(),
                'label' => $approver->getName()
            );
        }

        return $approvers;
    }
}
