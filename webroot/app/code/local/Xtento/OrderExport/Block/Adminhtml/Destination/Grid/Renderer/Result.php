<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2012-12-17T14:11:01+01:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Destination/Grid/Renderer/Result.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Destination_Grid_Renderer_Result extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if ($row->getLastResult() === NULL) {
            return '<span class="grid-severity-major"><span>' . Mage::helper('xtento_orderexport')->__('No Result') . '</span></span>';
        } else if ($row->getLastResult() == 0) {
            return '<span class="grid-severity-critical"><span>' . Mage::helper('xtento_orderexport')->__('Failed') . '</span></span>';
        } else if ($row->getLastResult() == 1) {
            return '<span class="grid-severity-notice"><span>' . Mage::helper('xtento_orderexport')->__('Success') . '</span></span>';
        }
    }
}