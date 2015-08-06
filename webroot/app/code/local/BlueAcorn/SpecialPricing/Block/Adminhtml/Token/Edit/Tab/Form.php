<?php
/**
 * BlueAcorn_SpecialPricing extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       BlueAcorn
 * @package        BlueAcorn_SpecialPricing
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Token edit form tab
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_SpecialPricing
 * @author      Ultimate Module Creator
 */
class BlueAcorn_SpecialPricing_Block_Adminhtml_Token_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return BlueAcorn_SpecialPricing_Block_Adminhtml_Token_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('token_');
        $form->setFieldNameSuffix('token');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'token_form',
            array('legend' => Mage::helper('blueacorn_specialpricing')->__('Token'))
        );

        $fieldset->addField(
            'token',
            'text',
            array(
                'label' => Mage::helper('blueacorn_specialpricing')->__('Token'),
                'name'  => 'token',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'product_id',
            'text',
            array(
                'label' => Mage::helper('blueacorn_specialpricing')->__('Product ID'),
                'name'  => 'product_id',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'token_expiration_date',
            'text',
            array(
                'label' => Mage::helper('blueacorn_specialpricing')->__('Token Expiration Date'),
                'name'  => 'token_expiration_date',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'quote_item_id',
            'text',
            array(
                'label' => Mage::helper('blueacorn_specialpricing')->__('Quote Item ID'),
                'name'  => 'quote_item_id',
            'required'  => true,
            'class' => 'required-entry',

           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('blueacorn_specialpricing')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('blueacorn_specialpricing')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('blueacorn_specialpricing')->__('Disabled'),
                    ),
                ),
            )
        );
        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                )
            );
            Mage::registry('current_token')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_token')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getTokenData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getTokenData());
            Mage::getSingleton('adminhtml/session')->setTokenData(null);
        } elseif (Mage::registry('current_token')) {
            $formValues = array_merge($formValues, Mage::registry('current_token')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
