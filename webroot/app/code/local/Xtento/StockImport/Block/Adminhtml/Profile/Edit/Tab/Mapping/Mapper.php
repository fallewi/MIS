<?php

/**
 * Product:       Xtento_StockImport (2.3.6)
 * ID:            Local Deploy
 * Packaged:      2016-10-18T22:31:59+02:00
 * Last Modified: 2013-07-20T18:08:08+02:00
 * File:          app/code/local/Xtento/StockImport/Block/Adminhtml/Profile/Edit/Tab/Mapping/Mapper.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Block_Adminhtml_Profile_Edit_Tab_Mapping_Mapper extends Xtento_StockImport_Block_Adminhtml_Profile_Edit_Tab_Mapping_Abstract
{
    protected $MAPPING_ID = 'mapping';
    protected $MAPPING_MODEL = 'xtento_stockimport/processor_mapping_fields';
    protected $VALUE_FIELD_NAME = 'Field Name / Index';
}
