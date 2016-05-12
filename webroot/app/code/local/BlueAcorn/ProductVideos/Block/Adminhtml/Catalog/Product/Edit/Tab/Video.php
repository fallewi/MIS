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
 * Product Video tab on product edit form
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Block_Adminhtml_Catalog_Product_Edit_Tab_Video
    extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * Set grid params
     * @access public
     *
     */
    public function __construct() {
        parent::__construct();
        $this->setId('video_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getProduct()->getId()) {
            $this->setDefaultFilter(array('in_videos'=>1));
        }
    }
    /**
     * prepare the video collection
     * @access protected
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Catalog_Product_Edit_Tab_Video
     *
     */
    protected function _prepareCollection() {
        $collection = Mage::getResourceModel('blueacorn_productvideos/video_collection');
        if ($this->getProduct()->getId()){
            $constraint = 'related.product_id='.$this->getProduct()->getId();
            }
            else{
                $constraint = 'related.product_id=0';
            }
        $collection->getSelect()->joinLeft(
            array('related'=>$collection->getTable('blueacorn_productvideos/video_product')),
            'related.video_id=main_table.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    /**
     * prepare mass action grid
     * @access protected
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Catalog_Product_Edit_Tab_Video
     *
     */
    protected function _prepareMassaction(){
        return $this;
    }
    /**
     * prepare the grid columns
     * @access protected
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Catalog_Product_Edit_Tab_Video
     *
     */
    protected function _prepareColumns(){
        $this->addColumn('in_videos', array(
            'header_css_class'  => 'a-center',
            'type'  => 'checkbox',
            'name'  => 'in_videos',
            'values'=> $this->_getSelectedVideos(),
            'align' => 'center',
            'index' => 'entity_id'
        ));
        $this->addColumn('title', array(
            'header'=> Mage::helper('blueacorn_productvideos')->__('Title'),
            'align' => 'left',
            'index' => 'title',
        ));
        $this->addColumn('position', array(
            'header'        => Mage::helper('blueacorn_productvideos')->__('Position'),
            'name'          => 'position',
            'width'         => 60,
            'type'        => 'number',
            'validate_class'=> 'validate-number',
            'index'         => 'position',
            'editable'      => true,
        ));
        return parent::_prepareColumns();
    }
    /**
     * Retrieve selected videos
     * @access protected
     * @return array
     *
     */
    protected function _getSelectedVideos(){
        $videos = $this->getProductVideos();
        if (!is_array($videos)) {
            $videos = array_keys($this->getSelectedVideos());
        }
        return $videos;
    }
     /**
     * Retrieve selected videos
     * @access protected
     * @return array
     *
     */
    public function getSelectedVideos() {
        $videos = array();
        //used helper here in order not to override the product model
        $selected = Mage::helper('blueacorn_productvideos/product')->getSelectedVideos(Mage::registry('current_product'));
        if (!is_array($selected)){
            $selected = array();
        }
        foreach ($selected as $video) {
            $videos[$video->getId()] = array('position' => $video->getPosition());
        }
        return $videos;
    }
    /**
     * get row url
     * @access public
     * @param BlueAcorn_ProductVideos_Model_Video
     * @return string
     *
     */
    public function getRowUrl($item){
        return '#';
    }
    /**
     * get grid url
     * @access public
     * @return string
     *
     */
    public function getGridUrl(){
        return $this->getUrl('*/*/videosGrid', array(
            'id'=>$this->getProduct()->getId()
        ));
    }
    /**
     * get the current product
     * @access public
     * @return Mage_Catalog_Model_Product
     *
     */
    public function getProduct(){
        return Mage::registry('current_product');
    }
    /**
     * Add filter
     * @access protected
     * @param object $column
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Catalog_Product_Edit_Tab_Video
     *
     */
    protected function _addColumnFilterToCollection($column){
        if ($column->getId() == 'in_videos') {
            $videoIds = $this->_getSelectedVideos();
            if (empty($videoIds)) {
                $videoIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$videoIds));
            }
            else {
                if($videoIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$videoIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
