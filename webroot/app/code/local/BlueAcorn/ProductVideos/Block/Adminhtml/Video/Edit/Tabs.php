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
 * Product Video admin edit tabs
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Block_Adminhtml_Video_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs {
    /**
     * Initialize Tabs
     * @access public
     *
     */
    public function __construct() {
        parent::__construct();
        $this->setId('video_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('blueacorn_productvideos')->__('Product Video'));
    }
    /**
     * before render html
     * @access protected
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Video_Edit_Tabs
     *
     */
    protected function _beforeToHtml(){
        $this->addTab('form_video', array(
            'label'        => Mage::helper('blueacorn_productvideos')->__('Product Video'),
            'title'        => Mage::helper('blueacorn_productvideos')->__('Product Video'),
            'content'     => $this->getLayout()->createBlock('blueacorn_productvideos/adminhtml_video_edit_tab_form')->toHtml(),
        ));
        if (!Mage::app()->isSingleStoreMode()){
            $this->addTab('form_store_video', array(
                'label'        => Mage::helper('blueacorn_productvideos')->__('Store views'),
                'title'        => Mage::helper('blueacorn_productvideos')->__('Store views'),
                'content'     => $this->getLayout()->createBlock('blueacorn_productvideos/adminhtml_video_edit_tab_stores')->toHtml(),
            ));
        }
        $this->addTab('products', array(
            'label' => Mage::helper('blueacorn_productvideos')->__('Associated products'),
            'url'   => $this->getUrl('*/*/products', array('_current' => true)),
            'class'    => 'ajax'
        ));
        return parent::_beforeToHtml();
    }
    /**
     * Retrieve product video entity
     * @access public
     * @return BlueAcorn_ProductVideos_Model_Video
     *
     */
    public function getVideo(){
        return Mage::registry('current_video');
    }
}
