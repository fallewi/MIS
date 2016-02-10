<?php

/**
 * @package     BlueAcorn\SpecialPricing
 * @version     1.0.4
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2016 Blue Acorn, Inc.
 */

class BlueAcorn_SpecialPricing_Block_Adminhtml_Token_Render_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        return Mage::getSingleton('core/date')->date('M j, Y g:i A', $value);

    }
}