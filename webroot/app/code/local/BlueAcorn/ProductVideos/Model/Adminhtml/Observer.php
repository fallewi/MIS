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
 * Adminhtml observer
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Model_Adminhtml_Observer {
    /**
     * check if tab can be added
     * @access protected
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     *
     */
    protected function _canAddTab($product){
        if ($product->getId()){
            return true;
        }
        if (!$product->getAttributeSetId()){
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable'){
            if ($request->getParam('attributes')){
                return true;
            }
        }
        return false;
    }
    /**
     * add the video tab to products
     * @access public
     * @param Varien_Event_Observer $observer
     * @return BlueAcorn_ProductVideos_Model_Adminhtml_Observer
     *
     */
    public function addProductVideoBlock($observer){
        $block = $observer->getEvent()->getBlock();
        $product = Mage::registry('product');
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs && $this->_canAddTab($product)){
            $block->addTab('videos', array(
                'label' => Mage::helper('blueacorn_productvideos')->__('Product Videos'),
                'url'   => Mage::helper('adminhtml')->getUrl('adminhtml/productvideos_video_catalog_product/videos', array('_current' => true)),
                'class' => 'ajax',
            ));
        }
        return $this;
    }
    /**
     * save video - product relation
     * @access public
     * @param Varien_Event_Observer $observer
     * @return BlueAcorn_ProductVideos_Model_Adminhtml_Observer
     *
     */
    public function saveProductVideoData($observer){
        $post = Mage::app()->getRequest()->getPost('videos', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $product = Mage::registry('product');
            $videoProduct = Mage::getResourceSingleton('blueacorn_productvideos/video_product')->saveProductRelation($product, $post);
        }
        return $this;
    }}
