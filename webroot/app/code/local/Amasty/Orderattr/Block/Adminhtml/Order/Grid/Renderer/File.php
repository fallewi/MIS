<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class Amasty_Orderattr_Block_Adminhtml_Order_Grid_Renderer_File extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $value = $this->_getValue($row);
        if ($value) {
            $path = Mage::getBaseDir('media') . DS . 'amorderattr' . DS . 'original' . $value;
            $url  = Mage::getBaseUrl('media') . 'amorderattr' . DS . 'original' . $value;
            if (file_exists($path)) {
                $pos = strrpos($value, "/");
                if ($pos) {
                    $value = substr($value, $pos + 1, strlen($value));
                }
                $value = '<a href="' . $url . '" download target="_blank">' . $value . '</a>';
            } else {
                $value = '';
            }
        }

        return $value;
    }
}
