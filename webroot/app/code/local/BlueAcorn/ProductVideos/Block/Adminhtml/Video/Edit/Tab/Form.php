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
 * Product Video edit form tab
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Block_Adminhtml_Video_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return ProductVideos_Video_Block_Adminhtml_Video_Edit_Tab_Form
     *
     */
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('video_');
        $form->setFieldNameSuffix('video');
        $this->setForm($form);
        $fieldset = $form->addFieldset('video_form', array('legend'=>Mage::helper('blueacorn_productvideos')->__('Product Video')));
        $fieldset->addType('file', Mage::getConfig()->getBlockClassName('blueacorn_productvideos/adminhtml_video_helper_file'));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('blueacorn_productvideos')->__('Title'),
            'name'  => 'title',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('description', 'textarea', array(
            'label' => Mage::helper('blueacorn_productvideos')->__('Description'),
            'name'  => 'description',

        ));

        $fieldset->addField('file', 'file', array(
            'label' => Mage::helper('blueacorn_productvideos')->__('File'),
            'name'  => 'file',
            'note'	=> $this->__('Use if hosting video file. Also note that the only supported video types are flv, mp4, ogg, ogv, swf, and webm.'),

        ));

        $fieldset->addField('url', 'text', array(
            'label' => Mage::helper('blueacorn_productvideos')->__('URL'),
            'name'  => 'url',
            'note'	=> $this->__('Use if video is hosted outside of Magento. Input the youtube/vimeo url here.'),

        ));

        $fieldset->addField('thumbnail', 'image', array(
            'label' => Mage::helper('blueacorn_productvideos')->__('Thumbnail'),
            'name'  => 'thumbnail',
            'note'	=> $this->__('Do not use if the video is hosted outside of Magento. The thumbnail image will be retrieved from the video host after save.'),

        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('blueacorn_productvideos')->__('Status'),
            'name'  => 'status',
            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('blueacorn_productvideos')->__('Enabled'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('blueacorn_productvideos')->__('Disabled'),
                ),
            ),
        ));
        if (Mage::app()->isSingleStoreMode()){
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('current_video')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $formValues = Mage::registry('current_video')->getDefaultValues();
        if (!is_array($formValues)){
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getVideoData()){
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getVideoData());
            Mage::getSingleton('adminhtml/session')->setVideoData(null);
        }
        elseif (Mage::registry('current_video')){
            $formValues = array_merge($formValues, Mage::registry('current_video')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
