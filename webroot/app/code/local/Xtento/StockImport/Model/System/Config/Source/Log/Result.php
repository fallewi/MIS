<?php

/**
 * Product:       Xtento_StockImport (2.3.6)
 * ID:            Local Deploy
 * Packaged:      2016-10-18T22:31:59+02:00
 * Last Modified: 2013-06-26T17:57:44+02:00
 * File:          app/code/local/Xtento/StockImport/Model/System/Config/Source/Log/Result.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Model_System_Config_Source_Log_Result
{
    public function toOptionArray()
    {
        $values = array();
        $values[Xtento_StockImport_Model_Log::RESULT_NORESULT] = Mage::helper('xtento_stockimport')->__('No Result');
        $values[Xtento_StockImport_Model_Log::RESULT_SUCCESSFUL] = Mage::helper('xtento_stockimport')->__('Successful');
        $values[Xtento_StockImport_Model_Log::RESULT_WARNING] = Mage::helper('xtento_stockimport')->__('Warning');
        $values[Xtento_StockImport_Model_Log::RESULT_FAILED] = Mage::helper('xtento_stockimport')->__('Failed');
        return $values;
    }
}