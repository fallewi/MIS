<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
if (Mage::helper('core')->isModuleEnabled('Amasty_Deliverydate')) {
    $autoloader = Varien_Autoload::instance();
    $autoloader->autoload('Amasty_Orderattr_Model_Sales_Order_Api_Deliverydate');
} else {
    class Amasty_Orderattr_Model_Sales_Order_Api_Pure extends Mage_Sales_Model_Order_Api {}
}
