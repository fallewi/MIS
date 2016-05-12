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
 * Product Video admin edit form
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Block_Adminhtml_Video_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * constructor
     * @access public
     * @return void
     *
     */
    public function __construct(){
        parent::__construct();
        $this->_blockGroup = 'blueacorn_productvideos';
        $this->_controller = 'adminhtml_video';
        $this->_updateButton('save', 'label', Mage::helper('blueacorn_productvideos')->__('Save Product Video'));
        $this->_updateButton('delete', 'label', Mage::helper('blueacorn_productvideos')->__('Delete Product Video'));
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('blueacorn_productvideos')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    /**
     * get the edit form header
     * @access public
     * @return string
     *
     */
    public function getHeaderText(){
        if( Mage::registry('current_video') && Mage::registry('current_video')->getId() ) {
            return Mage::helper('blueacorn_productvideos')->__("Edit Product Video '%s'", $this->escapeHtml(Mage::registry('current_video')->getTitle()));
        }
        else {
            return Mage::helper('blueacorn_productvideos')->__('Add Product Video');
        }
    }
}
