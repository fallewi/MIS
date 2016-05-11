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
 * Product Video admin block
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Block_Adminhtml_Video
    extends Mage_Adminhtml_Block_Widget_Grid_Container {
    /**
     * constructor
     * @access public
     * @return void
     *
     */
    public function __construct(){
        $this->_controller         = 'adminhtml_video';
        $this->_blockGroup         = 'blueacorn_productvideos';
        parent::__construct();
        $this->_headerText         = Mage::helper('blueacorn_productvideos')->__('Product Video');
        $this->_updateButton('add', 'label', Mage::helper('blueacorn_productvideos')->__('Add Product Video'));

    }
}
