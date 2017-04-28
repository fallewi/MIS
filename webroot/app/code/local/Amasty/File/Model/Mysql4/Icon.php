<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_File
 */
class Amasty_File_Model_Mysql4_Icon extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('amfile/icon', 'id');
    }

    public function getIcon($fileUrl) 
    {
        $fileInfo = new SplFileInfo($fileUrl);
        $select = $this->_getReadAdapter()
            ->select()
            ->from($this->getTable('amfile/icon'), 'image')
            ->where('active = 1')
            ->where('FIND_IN_SET(?, type)', $fileInfo->getExtension());

        return $this->_getReadAdapter()->fetchOne($select);
    }
}