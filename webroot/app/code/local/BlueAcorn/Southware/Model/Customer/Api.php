<?php
/**
 * @package BlueAcorn_Southware
 * @version 0.1.0
 * @author  BlueAcorn
 * @copyright Copyright (c) 2015 Blue Acorn, Inc.
 */

class BlueAcorn_Southware_Model_Customer_Api extends Mage_Customer_Model_Customer_Api
{
    /**
     * Function that sets the Southware Customer ID of a customer based on the email supplied by Southware
     *
     * @param $customerEmail
     * @param $southwareCustomerId
     */
    public function setSouthwareCustomerId($customerEmail, $southwareCustomerId)
    {
        $defaultStoreId = Mage::app()->getDefaultStoreView()->getId();
        $customer = Mage::getModel("customer/customer");

        $customer->setWebsiteId($defaultStoreId);
        $customer->loadByEmail($customerEmail);
        $customer->setSouthwareCustomerId($southwareCustomerId);

        $customer->save();
    }
}