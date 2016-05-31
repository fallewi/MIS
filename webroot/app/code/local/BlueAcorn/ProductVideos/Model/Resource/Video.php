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
 * Product Video resource model
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Model_Resource_Video
    extends Mage_Core_Model_Resource_Db_Abstract {
    /**
     * constructor
     * @access public
     *
     */
    public function _construct(){
        $this->_init('blueacorn_productvideos/video', 'entity_id');
    }
    /**
     * Get store ids to which specified item is assigned
     * @access public
     * @param int $videoId
     * @return array
     *
     */
    public function lookupStoreIds($videoId){
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('blueacorn_productvideos/video_store'), 'store_id')
            ->where('video_id = ?',(int)$videoId);
        return $adapter->fetchCol($select);
    }
    /**
     * Perform operations after object load
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return BlueAcorn_ProductVideos_Model_Resource_Video
     *
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object){
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }
        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param BlueAcorn_ProductVideos_Model_Video $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object){
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('productvideos_video_store' => $this->getTable('blueacorn_productvideos/video_store')),
                $this->getMainTable() . '.entity_id = productvideos_video_store.video_id',
                array()
            )
            ->where('productvideos_video_store.store_id IN (?)', $storeIds)
            ->order('productvideos_video_store.store_id DESC')
            ->limit(1);
        }
        return $select;
    }
    /**
     * Assign product video to store views
     * @access protected
     * @param Mage_Core_Model_Abstract $object
     * @return BlueAcorn_ProductVideos_Model_Resource_Video
     *
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object){
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('blueacorn_productvideos/video_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = array(
                'video_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );
            $this->_getWriteAdapter()->delete($table, $where);
        }
        if ($insert) {
            $data = array();
            foreach ($insert as $storeId) {
                $data[] = array(
                    'video_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }
            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }}
