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
 * Admin search model
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Model_Adminhtml_Search_Video
    extends Varien_Object {
    /**
     * Load search results
     * @access public
     * @return BlueAcorn_ProductVideos_Model_Adminhtml_Search_Video
     *
     */
    public function load(){
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('blueacorn_productvideos/video_collection')
            ->addFieldToFilter('title', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $video) {
            $arr[] = array(
                'id'=> 'video/1/'.$video->getId(),
                'type'  => Mage::helper('blueacorn_productvideos')->__('Product Video'),
                'name'  => $video->getTitle(),
                'description'   => $video->getTitle(),
                'url' => Mage::helper('adminhtml')->getUrl('*/productvideos_video/edit', array('id'=>$video->getId())),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
