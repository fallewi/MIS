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
 * Product Video - product relation resource model collection
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Model_Resource_Video_Product_Collection
    extends Mage_Catalog_Model_Resource_Product_Collection {
    /**
     * remember if fields have been joined
     * @var bool
     */
    protected $_joinedFields = false;
    /**
     * join the link table
     * @access public
     * @return BlueAcorn_ProductVideos_Model_Resource_Video_Product_Collection
     *
     */
    public function joinFields(){
        if (!$this->_joinedFields){
            $this->getSelect()->join(
                array('related' => $this->getTable('blueacorn_productvideos/video_product')),
                'related.product_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }
    /**
     * add video filter
     * @access public
     * @param BlueAcorn_ProductVideos_Model_Video | int $video
     * @return BlueAcorn_ProductVideos_Model_Resource_Video_Product_Collection
     *
     */
    public function addVideoFilter($video){
        if ($video instanceof BlueAcorn_ProductVideos_Model_Video){
            $video = $video->getId();
        }
        if (!$this->_joinedFields){
            $this->joinFields();
        }
        $this->getSelect()->where('related.video_id = ?', $video);
        return $this;
    }
}
