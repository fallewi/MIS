<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class Amasty_Orderattr_Block_Adminhtml_Order_Grid_Renderer_Datetime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime
{
    public function render(Varien_Object $row)
    {
        if ($data = $this->_getValue($row)) {
            if ($data === '0000-00-00' ||
                $data === '0000-00-00 00:00:00' ||
                $data === '1970-01-01' ||
                $data === '1970-01-01 00:00:00'
            ) {
                return '';
            }

            if (Mage::getStoreConfig('amorderattr/general/default_format')) {
                $format = $this->_getFormat();
            } else {
                $format = Mage::getStoreConfig('amorderattr/general/datetime_format');
            }

            $collection = Mage::getModel('eav/entity_attribute')
                ->getCollection()
                ->addFieldToFilter('entity_type_id', Mage::getModel('eav/entity')->setType('order')->getTypeId())
                ->addFieldToFilter('attribute_code', $this->getColumn()->getIndex());
            $attribute = $collection->getFirstItem();

            try {
                if ($attribute && 'time' != $attribute->getNote()) {
                    $format = trim(str_replace(array('m', 'a', 'H', ':', 'h', 's'), '', $format));
                    $data = Mage::app()->getLocale()->date($data, Varien_Date::DATE_INTERNAL_FORMAT, null, false)->toString($format);
                } else {
                    $data = Mage::app()->getLocale()->date($data, Varien_Date::DATETIME_INTERNAL_FORMAT, null, false)->toString($format);
                }
            }
            catch (Exception $e)
            {
                $data = Mage::app()->getLocale()->date($data, Varien_Date::DATETIME_INTERNAL_FORMAT, null, false)->toString($format);
            }
            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}
