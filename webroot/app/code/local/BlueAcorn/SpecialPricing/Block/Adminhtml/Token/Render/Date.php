<?php

class BlueAcorn_SpecialPricing_Block_Adminhtml_Token_Render_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        return Mage::getSingleton('core/date')->date('M j, Y g:i A', $value);

    }
}