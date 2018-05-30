<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


$autoloader = Varien_Autoload::instance();
if (Mage::helper('core')->isModuleEnabled('Amasty_Customerattr')) {
    $autoloader->autoload('Amasty_Orderattr_Block_Adminhtml_Order_Create_Form_Account_Customerattr');
} else {
    class Amasty_Orderattr_Block_Adminhtml_Order_Create_Form_Account_Pure extends Mage_Adminhtml_Block_Sales_Order_Create_Form_Account {}
}
