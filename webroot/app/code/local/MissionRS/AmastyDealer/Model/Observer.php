<?php

/**
 * Class MissionRS_AmastyDealer_Model_Observer
 */
class MissionRS_AmastyDealer_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     */
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

    /**
     * @param Varien_Event_Observer $observer
     */
    public function salesOrderPlaceAfter(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $customerId = (int) $order->getCustomerId();
        $adminId = (int) Mage::getResourceModel('amperm/perm')->getUserByCustomer($customerId);

        $adminUserName = Mage::getModel('admin/user')->load($adminId)->getUsername();

        if ($adminUserName) {
            $order = $observer->getOrder();
            $order->setAdminId($adminUserName)->save();
        }
    }
}
