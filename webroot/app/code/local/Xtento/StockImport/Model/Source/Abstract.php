<?php

/**
 * Product:       Xtento_StockImport (2.3.6)
 * ID:            Local Deploy
 * Packaged:      2016-10-18T22:31:59+02:00
 * Last Modified: 2013-07-28T13:00:42+02:00
 * File:          app/code/local/Xtento/StockImport/Model/Source/Abstract.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

abstract class Xtento_StockImport_Model_Source_Abstract extends Mage_Core_Model_Abstract implements Xtento_StockImport_Model_Source_Interface
{
    protected $_connection;
}