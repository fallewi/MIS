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
 * Product Video - category controller
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
require_once ("Mage/Adminhtml/controllers/Catalog/CategoryController.php");
class BlueAcorn_ProductVideos_Adminhtml_Productvideos_Video_Catalog_CategoryController
    extends Mage_Adminhtml_Catalog_CategoryController {
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
     * videos grid in the catalog page
     * @access public
     * @return void
     *
     */
    public function videosgridAction(){
        $this->_initCategory();
        $this->loadLayout();
        $this->getLayout()->getBlock('category.edit.tab.video')
            ->setCategoryVideos($this->getRequest()->getPost('category_videos', null));
        $this->renderLayout();
    }
}
