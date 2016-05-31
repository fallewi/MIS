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
 * Product Video - product relation model
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Model_Resource_Video_Product
    extends Mage_Core_Model_Resource_Db_Abstract {
    /**
     * initialize resource model
     * @access protected
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     *
     */
    protected function  _construct(){
        $this->_init('blueacorn_productvideos/video_product', 'rel_id');
    }
    /**
     * Save video - product relations
     * @access public
     * @param BlueAcorn_ProductVideos_Model_Video $video
     * @param array $data
     * @return BlueAcorn_ProductVideos_Model_Resource_Video_Product
     *
     */
    public function saveVideoRelation($video, $data){
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('video_id=?', $video->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $productId => $info) {
            $this->_getWriteAdapter()->insert($this->getMainTable(), array(
                'video_id'      => $video->getId(),
                'product_id'     => $productId,
                'position'      => @$info['position']
            ));
        }
        return $this;
    }
    /**
     * Save  product - video relations
     * @access public
     * @param Mage_Catalog_Model_Product $prooduct
     * @param array $data
     * @return BlueAcorn_ProductVideos_Model_Resource_Video_Product
     * @
     */
    public function saveProductRelation($product, $data){
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('product_id=?', $product->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $videoId => $info) {
            $this->_getWriteAdapter()->insert($this->getMainTable(), array(
                'video_id' => $videoId,
                'product_id' => $product->getId(),
                'position'   => @$info['position']
            ));
        }
        return $this;
    }
}
