<?php
/**
 * @category    Levementum
 * @package     Levementum_AdminOrders
 * @file        Data.php
 * @author      icoast@levementum.com
 * @date        10/16/13 12:36 PM
 * @brief
 * @details
 */

class Levementum_AdminOrders_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getSalespersonRoleName()
    {
        $configRoleAttributeID = Mage::getStoreConfig('sales/adminorders/salesperson_role');
        return Mage::getModel('admin/user')->load($configRoleAttributeID)->getRole()->getRoleName();
    }

    public function isSalesperson()
    {
        if (!Mage::getModel('admin/session')->isLoggedIn()) {
            return false;
        }

        return ((Mage::getModel('admin/session')->getUser()->getRole()->getRoleName() == $this->getSalespersonRoleName())
	        || (Mage::getModel('admin/session')->getUser()->getRole()->getRoleName() == "Super User"));
    }

    /**
     * Change dbeljic@levementum.com on  at
     * Description: checks is admin have admin role
     *
     * @return boolean
     */
    public function isAdmin()
    {
        if (!Mage::getModel('admin/session')->isLoggedIn()) {
            return false;
        }

        return (Mage::getModel('admin/session')->getUser()->getRole()->getRoleName() == "Super User");
    }


    /**
     * Change dbeljic@levementum.com on  at
     * Description: returns customer assigned salesperson
     *
     * @return string
     */
    public function getAssignedSalesperson() {

        $customerId = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();

        if($customerId) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            if($customer) {
                return  $customer->getData('assigned_salesperson');
            }
        }
        return '';
    }

    /**
     * Change dbeljic@levementum.com on  at
     * Description: returns customer assigned salesperson name
     *
     * @return string
     */
    public function getAssignedSalespersonName() {

        $customerId = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();

        if($customerId) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            if($customer) {
                return $customer->getResource()->getAttribute('assigned_salesperson')->getFrontend()->getValue($customer);
            }
        }
        return '';
    }



    /**
     * Change dbeljic@levementum.com on  at
     * Description: returns customer customer_southware_customer_id
     *
     * @return string
     */
    public function getCustomerSouthwareCustomerId($order) {

        /* if order has an order id, it also has s customer id */
        if ($_customer_southware_customer_id = $order->getData('customer_southware_customer_id')) {
            return $_customer_southware_customer_id;
        }

        $customerId = $order->getCustomerId();

        if($customerId) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            if($customer) {
                return $customer->getData('southware_customer_id');
            }
        }
        return '';
    }



}