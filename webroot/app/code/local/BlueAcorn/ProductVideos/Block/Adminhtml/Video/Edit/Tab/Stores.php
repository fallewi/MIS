<?php
/**
 * BlueAcorn_ProductVideos extension
 * 
 *
 * @category       BlueAcorn
 * @package        BlueAcorn_ProductVideos
 * @author         Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright      Copyright © 2014 Blue Acorn, Inc.
 */
/**
 * store selection tab
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Block_Adminhtml_Video_Edit_Tab_Stores
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Video_Edit_Tab_Stores
     *
     */
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('video');
        $this->setForm($form);
        $fieldset = $form->addFieldset('video_stores_form', array('legend'=>Mage::helper('blueacorn_productvideos')->__('Store views')));
        $field = $fieldset->addField('store_id', 'multiselect', array(
            'name'  => 'stores[]',
            'label' => Mage::helper('blueacorn_productvideos')->__('Store Views'),
            'title' => Mage::helper('blueacorn_productvideos')->__('Store Views'),
            'required'  => true,
            'values'=> Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
        ));
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $field->setRenderer($renderer);
          $form->addValues(Mage::registry('current_video')->getData());
        return parent::_prepareForm();
    }
}
