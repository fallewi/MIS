<?php

/**
 * Product:       Xtento_StockImport (2.3.6)
 * ID:            Local Deploy
 * Packaged:      2016-10-18T22:31:59+02:00
 * Last Modified: 2013-06-26T17:57:44+02:00
 * File:          app/code/local/Xtento/StockImport/Block/Adminhtml/Tools.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Block_Adminhtml_Tools extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('xtento/stockimport/tools.phtml');
    }

    protected function _toHtml()
    {
        return $this->getLayout()->createBlock('xtento_stockimport/adminhtml_widget_menu')->toHtml() . parent::_toHtml();
    }
}