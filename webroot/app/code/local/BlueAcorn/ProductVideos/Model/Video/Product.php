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
 * Product Video product model
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Model_Video_Product
    extends Mage_Core_Model_Abstract {
    /**
     * Initialize resource
     * @access protected
     * @return void
     *
     */
    protected function _construct(){
        $this->_init('blueacorn_productvideos/video_product');
    }
    /**
     * Save data for video-product relation
     * @access public
     * @param  BlueAcorn_ProductVideos_Model_Video $video
     * @return BlueAcorn_ProductVideos_Model_Video_Product
     *
     */
    public function saveVideoRelation($video){
        $data = $video->getProductsData();
        if (!is_null($data)) {
            $this->_getResource()->saveVideoRelation($video, $data);
        }
        return $this;
    }
    /**
     * get products for video
     * @access public
     * @param BlueAcorn_ProductVideos_Model_Video $video
     * @return BlueAcorn_ProductVideos_Model_Resource_Video_Product_Collection
     *
     */
    public function getProductCollection($video){
        $collection = Mage::getResourceModel('blueacorn_productvideos/video_product_collection')
            ->addVideoFilter($video);
        return $collection;
    }
}
