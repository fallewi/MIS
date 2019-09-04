<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2012-12-21T15:51:38+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/History.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_History extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'xtento_orderexport';
        $this->_controller = 'adminhtml_history';
        $this->_headerText = Mage::helper('xtento_orderexport')->__('Sales Export - Export History');
        parent::__construct();
        $this->_removeButton('add');
    }

    protected function _toHtml()
    {
        return $this->getLayout()->createBlock('xtento_orderexport/adminhtml_widget_menu')->toHtml() . parent::_toHtml();
    }
}