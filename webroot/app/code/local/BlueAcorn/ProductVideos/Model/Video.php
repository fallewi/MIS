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
 * Product Video model
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Model_Video
    extends Mage_Core_Model_Abstract {
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'blueacorn_productvideos_video';
    const CACHE_TAG = 'blueacorn_productvideos_video';
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'blueacorn_productvideos_video';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'video';
    protected $_productInstance = null;
    /**
     * constructor
     * @access public
     * @return void
     *
     */
    public function _construct(){
        parent::_construct();
        $this->_init('blueacorn_productvideos/video');
    }
    /**
     * before save product video
     * @access protected
     * @return BlueAcorn_ProductVideos_Model_Video
     *
     */
    protected function _beforeSave(){
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()){
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }
    /**
     * save video relation
     * @access public
     * @return BlueAcorn_ProductVideos_Model_Video
     *
     */
    protected function _afterSave() {
        $this->getProductInstance()->saveVideoRelation($this);
        return parent::_afterSave();
    }
    /**
     * get product relation model
     * @access public
     * @return BlueAcorn_ProductVideos_Model_Video_Product
     *
     */
    public function getProductInstance(){
        if (!$this->_productInstance) {
            $this->_productInstance = Mage::getSingleton('blueacorn_productvideos/video_product');
        }
        return $this->_productInstance;
    }
    /**
     * get selected products array
     * @access public
     * @return array
     *
     */
    public function getSelectedProducts(){
        if (!$this->hasSelectedProducts()) {
            $products = array();
            foreach ($this->getSelectedProductsCollection() as $product) {
                $products[] = $product;
            }
            $this->setSelectedProducts($products);
        }
        return $this->getData('selected_products');
    }
    /**
     * Retrieve collection selected products
     * @access public
     * @return BlueAcorn_ProductVideos_Resource_Video_Product_Collection
     *
     */
    public function getSelectedProductsCollection(){
        $collection = $this->getProductInstance()->getProductCollection($this);
        return $collection;
    }
    /**
     * get default values
     * @access public
     * @return array
     *
     */
    public function getDefaultValues() {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
}
