<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class  MissionRS_AmastyOrderAttributeFix_Model_PaypalObserver extends Amasty_Orderattr_Model_PaypalObserver
{
    protected function _applyDefaultValues($order, $attributes)
    {
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')
                        ->setEntityTypeFilter( Mage::getModel('eav/entity')->setType('order')->getTypeId() );
        $collection->getSelect()
            ->where('main_table.is_user_defined = ?', 1)
            ->where('main_table.apply_default = ?', 1);
        if ($collection->getSize() > 0)
        {
            foreach ($collection as $attributeToApply)
            {
                // VHC additional logic for hidden field for MKT tracking last in
                if ("tracking_last" == $attributeToApply->getAttributeCode())
                {
                    $strMKTlast = Mage::getModel('core/cookie')->get('mrsc01');
                    $attributes->setData($attributeToApply->getAttributeCode(),$strMKTlast);
                }
                // VHC additional logic for hidden field for MKT tracking first in
                elseif ("tracking_first" == $attributeToApply->getAttributeCode())
                {
                    $strMKTfirst = Mage::getModel('core/cookie')->get('mrsc90');
                    $attributes->setData($attributeToApply->getAttributeCode(),$strMKTfirst);
                }
                elseif(!$attributes->getData($attributeToApply->getAttributeCode()) && $attributeToApply->getDefaultValue())
                {
                    $attributes->setData($attributeToApply->getAttributeCode(), $attributeToApply->getDefaultValue());
                }
            }
        }
    }
}
