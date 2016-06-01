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
 * Product Video admin widget chooser
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */

class BlueAcorn_ProductVideos_Block_Adminhtml_Video_Widget_Chooser
    extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * Block construction, prepare grid params
     * @access public
     * @param array $arguments Object data
     * @return void
     *
     */
    public function __construct($arguments=array()){
        parent::__construct($arguments);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('chooser_status' => '1'));
    }
    /**
     * Prepare chooser element HTML
     * @access public
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     *
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element){
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('blueacorn_productvideos/adminhtml_productvideos_video_widget/chooser', array('uniq_id' => $uniqId));
        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
                ->setElement($element)
                ->setTranslationHelper($this->getTranslationHelper())
                ->setConfig($this->getConfig())
                ->setFieldsetId($this->getFieldsetId())
                ->setSourceUrl($sourceUrl)
                ->setUniqId($uniqId);
        if ($element->getValue()) {
            $video = Mage::getModel('blueacorn_productvideos/video')->load($element->getValue());
            if ($video->getId()) {
                $chooser->setLabel($video->getTitle());
            }
        }
        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }
    /**
     * Grid Row JS Callback
     * @access public
     * @return string
     *
     */
    public function getRowClickCallback(){
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var videoId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var videoTitle = trElement.down("td").next().innerHTML;
                '.$chooserJsObject.'.setElementValue(videoId);
                '.$chooserJsObject.'.setElementLabel(videoTitle);
                '.$chooserJsObject.'.close();
            }
        ';
        return $js;
    }
    /**
     * Prepare a static blocks collection
     * @access protected
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Video_Widget_Chooser
     *
     */
    protected function _prepareCollection(){
        $collection = Mage::getModel('blueacorn_productvideos/video')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * Prepare columns for the a grid
     * @access protected
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Video_Widget_Chooser
     *
     */
    protected function _prepareColumns(){
        $this->addColumn('chooser_id', array(
            'header'    => Mage::helper('blueacorn_productvideos')->__('Id'),
            'align'     => 'right',
            'index'     => 'entity_id',
            'type'        => 'number',
            'width'     => 50
        ));

        $this->addColumn('chooser_title', array(
            'header'=> Mage::helper('blueacorn_productvideos')->__('Title'),
            'align' => 'left',
            'index' => 'title',
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'=> Mage::helper('blueacorn_productvideos')->__('Store Views'),
                'index' => 'store_id',
                'type'  => 'store',
                'store_all' => true,
                'store_view'=> true,
                'sortable'  => false,
            ));
        }
        $this->addColumn('chooser_status', array(
            'header'=> Mage::helper('blueacorn_productvideos')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'options'   => array(
                0 => Mage::helper('blueacorn_productvideos')->__('Disabled'),
                1 => Mage::helper('blueacorn_productvideos')->__('Enabled')
            ),
        ));
        return parent::_prepareColumns();
    }
    /**
     * get url for grid
     * @access public
     * @return string
     *
     */
    public function getGridUrl(){
        return $this->getUrl('adminhtml/productvideos_video_widget/chooser', array('_current' => true));
    }
    /**
     * after collection load
     * @access protected
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Video_Widget_Chooser
     *
     */
    protected function _afterLoadCollection(){
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
