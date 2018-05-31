<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class Amasty_Orderattr_Block_Adminhtml_Order_Grid_Renderer_Checkboxes
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox
{
    public function render(Varien_Object $row)
    {
        if ($data = $this->_getValue($row)) {
            $attributeCode = $this->getColumn()->getId();
            $attribute = Mage::getModel('eav/entity_attribute')->load($attributeCode, 'attribute_code');
            $source = $attribute->getSource();

            $data = explode(',', $data);

            foreach ($data as $key => $value) {
                if ($optionText = $source->getOptionText($value)) {
                   $data[$key] = $optionText;
                }
            }

            $data = implode(', ', $data);

            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}