<?php
/**
 * @category    Levementum
 * @package     Levementum_
 * @file        Observer.php
 * @author      icoast@levementum.com
 * @date        10/16/13 1:37 PM
 * @brief       
 * @details     
 */

class Levementum_AdminOrders_Model_Observer {
    public function adminCheckoutSubmitAllAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getOrder();

        $postData = Mage::app()->getRequest()->getPost('order');
        if (!isset($postData['admin_id'])) {
            return;
        }

        $adminId = $postData['admin_id'];
        if ($adminId) {
            $order->setAdminId($adminId)->save();
        }
    }
}