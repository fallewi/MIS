<?php

/**
 * Product:       Xtento_StockImport (2.3.6)
 * ID:            Local Deploy
 * Packaged:      2016-10-18T22:31:59+02:00
 * Last Modified: 2013-07-13T13:03:02+02:00
 * File:          app/code/local/Xtento/StockImport/Model/Mysql4/Profile.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Model_Mysql4_Profile extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_serializableFields = array(
        'configuration' => array(null, array())
    );

    protected function _construct()
    {
        $this->_init('xtento_stockimport/profile', 'profile_id');
    }
}