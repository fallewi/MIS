<?php

class Cminds_MultiUserAccounts_Model_Customer_Api extends Mage_Api_Model_Resource_Abstract
{


    /**
     * API method to create subaccounts.
     *
     * @param $args
     * @return mixed|SoapFault
     */
    public function createSubAccount($args)
    {
        $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount');
        if (is_object($args)) {
            $args = (array)$args;
        }

        $subAccount->setWebsiteId($args['website_id'])
            ->setStoreId($args['store_id'])
            ->setFirstname($args['firstname'])
            ->setLastname($args['lastname'])
            ->setEmail($args['email'])
            ->setPassword($args['password'])
            ->setParentCustomerId($args['parent_customer_id'])
            ->setPermission($args['permission'])
            ->setViewAllOrders($args['view_all_orders'])
            ->setCanSeeCart($args['can_see_cart'])
            ->setHaveAccessCheckout($args['have_access_checkout'])
            ->setGetOrderEmail($args['get_order_email'])
            ->setGetOrderInvoice($args['get_order_invoice'])
            ->setGetOrderShipment($args['get_order_shipment']);
        try {
            $subAccount->save();
            return $subAccount->getData();
        } catch (Exception $e) {
            return new SoapFault(null, $e->getMessage());
        }
    }


    /**
     * API method to edit subaccounts.
     *
     * @param $args
     * @return mixed|SoapFault
     */
    public function editSubAccount($args)
    {
        if (is_object($args)) {
            $args = (array)$args;
        }

        $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($args['customer_id'], 'customer_id');

        if (!empty($args['password'])) {
            $newPassword = $args['password'];
            $subAccount->setPassword($newPassword);
        }
        unset($args['password']);

        $subAccount->addData($args);

        try {
            $subAccount->save();
            return $subAccount->getData();
        } catch (Exception $e) {
            return new SoapFault(null, $e->getMessage());
        }
    }


    /**
     * API method to delete subaccounts.
     *
     * @param $args
     * @return array|SoapFault
     */
    public function deleteSubAccount($args)
    {
        $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount');
        if (is_object($args)) {
            $args = (array)$args;
        }

        $subAccount->load($args['subaccount_id'], 'customer_id');
        try {
            $responseData = $subAccount->getData();
            $subAccount->delete();
            Mage::log('Subaccount with email "'.$responseData['email'].'" has been removed correctly.', null, 'subaccount_api_delete.log');
            return $subAccount->getData();
        } catch (Exception $e) {
            return new SoapFault(null, $e->getMessage());
        }
    }
}