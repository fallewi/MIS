<?php

/**
 * Product:       Xtento_StockImport (2.3.6)
 * ID:            Local Deploy
 * Packaged:      2016-10-18T22:31:59+02:00
 * Last Modified: 2013-09-03T10:59:37+02:00
 * File:          app/code/local/Xtento/StockImport/Model/System/Config/Source/Stock/Reindex.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Model_System_Config_Source_Stock_Reindex
{

    public function toOptionArray()
    {
        $modes[] = array('value' => 'no_reindex', 'label' => Mage::helper('xtento_stockimport')->__('No reindexing at all. (Recommended)'));
        $modes[] = array('value' => 'flag_index', 'label' => Mage::helper('xtento_stockimport')->__('No reindex. Flag as \'reindex required\'. (1.4+)'));
        $modes[] = array('value' => 'full', 'label' => Mage::helper('xtento_stockimport')->__('Full reindex (after import)'));
        //$modes[] = array('value' => 'changed', 'label' => Mage::helper('inventoryimport')->__('Changed only (1.4+, during import)'));
        return $modes;
    }

}
