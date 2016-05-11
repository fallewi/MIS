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
 * Product Video admin grid block
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Block_Adminhtml_Video_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * constructor
     * @access public
     *
     */
    public function __construct(){
        parent::__construct();
        $this->setId('videoGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    /**
     * prepare collection
     * @access protected
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Video_Grid
     *
     */
    protected function _prepareCollection(){
        $collection = Mage::getModel('blueacorn_productvideos/video')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * prepare grid collection
     * @access protected
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Video_Grid
     *
     */
    protected function _prepareColumns(){
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('blueacorn_productvideos')->__('Id'),
            'index'        => 'entity_id',
            'type'        => 'number'
        ));
        $this->addColumn('title', array(
            'header'    => Mage::helper('blueacorn_productvideos')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('blueacorn_productvideos')->__('Status'),
            'index'        => 'status',
            'type'        => 'options',
            'options'    => array(
                '1' => Mage::helper('blueacorn_productvideos')->__('Enabled'),
                '0' => Mage::helper('blueacorn_productvideos')->__('Disabled'),
            )
        ));
        if (!Mage::app()->isSingleStoreMode() && !$this->_isExport) {
            $this->addColumn('store_id', array(
                'header'=> Mage::helper('blueacorn_productvideos')->__('Store Views'),
                'index' => 'store_id',
                'type'  => 'store',
                'store_all' => true,
                'store_view'=> true,
                'sortable'  => false,
                'filter_condition_callback'=> array($this, '_filterStoreCondition'),
            ));
        }
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('blueacorn_productvideos')->__('Created at'),
            'index'     => 'created_at',
            'width'     => '120px',
            'type'      => 'datetime',
        ));
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('blueacorn_productvideos')->__('Updated at'),
            'index'     => 'updated_at',
            'width'     => '120px',
            'type'      => 'datetime',
        ));
        $this->addColumn('action',
            array(
                'header'=>  Mage::helper('blueacorn_productvideos')->__('Action'),
                'width' => '100',
                'type'  => 'action',
                'getter'=> 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('blueacorn_productvideos')->__('Edit'),
                        'url'   => array('base'=> '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter'=> false,
                'is_system'    => true,
                'sortable'  => false,
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('blueacorn_productvideos')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('blueacorn_productvideos')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('blueacorn_productvideos')->__('XML'));
        return parent::_prepareColumns();
    }
    /**
     * prepare mass action
     * @access protected
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Video_Grid
     *
     */
    protected function _prepareMassaction(){
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('video');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('blueacorn_productvideos')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('blueacorn_productvideos')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('blueacorn_productvideos')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'status' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('blueacorn_productvideos')->__('Status'),
                        'values' => array(
                                '1' => Mage::helper('blueacorn_productvideos')->__('Enabled'),
                                '0' => Mage::helper('blueacorn_productvideos')->__('Disabled'),
                        )
                )
            )
        ));
        return $this;
    }
    /**
     * get the row url
     * @access public
     * @param BlueAcorn_ProductVideos_Model_Video
     * @return string
     *
     */
    public function getRowUrl($row){
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    /**
     * get the grid url
     * @access public
     * @return string
     *
     */
    public function getGridUrl(){
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    /**
     * after collection load
     * @access protected
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Video_Grid
     *
     */
    protected function _afterLoadCollection(){
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
    /**
     * filter store column
     * @access protected
     * @param BlueAcorn_ProductVideos_Model_Resource_Video_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return BlueAcorn_ProductVideos_Block_Adminhtml_Video_Grid
     *
     */
    protected function _filterStoreCondition($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->addStoreFilter($value);
        return $this;
    }
}
