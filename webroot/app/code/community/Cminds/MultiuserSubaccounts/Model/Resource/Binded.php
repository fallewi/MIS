<?php

class Cminds_MultiuserSubaccounts_Model_Resource_Binded extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('cminds_multiusersubaccounts/binded', 'id');
    }
}