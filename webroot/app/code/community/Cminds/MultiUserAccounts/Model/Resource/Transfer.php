<?php

class Cminds_MultiUserAccounts_Model_Resource_Transfer extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('cminds_multiuseraccounts/transfer', 'entity_id');
    }

}
