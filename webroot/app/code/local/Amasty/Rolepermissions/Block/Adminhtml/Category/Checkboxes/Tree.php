<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


class Amasty_Rolepermissions_Block_Adminhtml_Category_Checkboxes_Tree extends Mage_Adminhtml_Block_Catalog_Category_Checkboxes_Tree
{
    protected function _getNodeJson($node, $level = 1)
    {
        $item = parent::_getNodeJson($node, $level);

        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($rule->getCategories() && !in_array($item['id'], $rule->getCategories()))
        {
            $item['disabled'] = true;
            $item['allowChildren'] = false;
        }

        return $item;
    }
}
