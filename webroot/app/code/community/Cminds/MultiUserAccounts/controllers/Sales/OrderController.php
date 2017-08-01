<?php
/**
 * Sales orders controller
 *
 * @author      CreativeMindsSolutions
 */

require_once('Mage/Sales/controllers/OrderController.php');

class Cminds_MultiUserAccounts_Sales_OrderController extends Mage_Sales_OrderController
{
    /**
     * Check order view availability
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  bool
     */
    protected function _canViewOrder($order)
    {
        $helper = Mage::helper('cminds_multiuseraccounts');
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $availableStates = Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates();

        if (($order->getId() && $order->getCustomerId() && $helper->isSubAccountMode() && !$helper->ifShareSession())
            && in_array($order->getState(), $availableStates, $strict = true)
        ) {
            if ($helper->isSubAccountMode()->getParentCustomerId() != $order->getCustomerId()) {
                return false;
            }
            if ($helper->canViewAllOrders()) {
                return true;
            } else {
                if ($helper->isSubAccountMode()->getId() != $order->getSubaccountId()) {
                    return false;
                }
                return true;
            }
        }

        if ($order->getId() && $order->getCustomerId() && ($order->getCustomerId() == $customerId)
            && in_array($order->getState(), $availableStates, $strict = true)
        ) {
            if ($subAccount = $helper->isSubAccountMode()) {
                if (!$helper->canViewAllOrders()) {
                    if ($subAccount->getId() != $order->getSubaccountId()) {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }
}
