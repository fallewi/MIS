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
 * Product Video - product controller
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
require_once ("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class BlueAcorn_ProductVideos_Adminhtml_Productvideos_Video_Catalog_ProductController
    extends Mage_Adminhtml_Catalog_ProductController {
    /**
     * construct
     * @access protected
     * @return void
     *
     */
    protected function _construct(){
        // Define module dependent translate
        $this->setUsedModuleName('BlueAcorn_ProductVideos');
    }
    /**
     * videos in the catalog page
     * @access public
     * @return void
     *
     */
    public function videosAction(){
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('product.edit.tab.video')
            ->setProductVideos($this->getRequest()->getPost('product_videos', null));
        $this->renderLayout();
    }
    /**
     * videos grid in the catalog page
     * @access public
     * @return void
     *
     */
    public function videosGridAction(){
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('product.edit.tab.video')
            ->setProductVideos($this->getRequest()->getPost('product_videos', null));
        $this->renderLayout();
    }
}
