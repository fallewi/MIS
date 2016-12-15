<?php

/**
 * Product:       Xtento_StockImport (2.3.6)
 * ID:            Local Deploy
 * Packaged:      2016-10-18T22:31:59+02:00
 * Last Modified: 2013-06-26T18:02:10+02:00
 * File:          app/code/local/Xtento/StockImport/Model/System/Config/Backend/Import/Enabled.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Model_System_Config_Backend_Import_Enabled extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        Mage::register('stockimport_modify_event', true, true);
        parent::_beforeSave();
    }

    public function has_value_for_configuration_changed($observer)
    {
        if (Mage::registry('stockimport_modify_event') == true) {
            Mage::unregister('stockimport_modify_event');
            Xtento_StockImport_Model_System_Config_Source_Order_Status::isEnabled();
        }
    }
}
