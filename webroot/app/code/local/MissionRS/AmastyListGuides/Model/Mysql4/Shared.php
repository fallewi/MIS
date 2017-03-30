<?php
 
class MissionRS_AmastyListGuides_Model_Mysql4_Shared extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('amlistl/shared', 'item_id');
    }   
}