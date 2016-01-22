<?php
/**
 * MissionRS_Purchaseorder_Model_Payment_Method_Purchaseorder
 *
 * @category    MissionRS
 * @package     MissionRS_Purchaseorder
 * @author 		Victor Cortez <victorc@missionrs.com>
 */
class MissionRS_Purchaseorder_Model_Payment_Method_Purchaseorder extends Mage_Payment_Model_Method_Purchaseorder
{
	/**
	 * Added constructor for Purchaseorder payment method.
	 * Disable the payment method for certain attribute groups.
	 */
	public function __construct()
	{
		parent::__construct();

        $this->_canUseCheckout = false;

        //retrieve enabled customer groups
        $customerGroups = explode(',', $this->getConfigData('customer_groups'));

		//check store
        if(!Mage::app()->getStore()->isAdmin())
        {
            //if store is not admin, and if customer group does match enabled customer groups set $this->_canUseCheckout = false;
            if(Mage::getSingleton('customer/session')->isLoggedIn())
            {
                //retrieve customer group from quote
                $currentCustomerGroup = Mage::getSingleton('checkout/session')->getQuote()->getCustomerGroupId();
            }
            else
            {
                $currentCustomerGroup = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
            }

            foreach($customerGroups as $customerGroup)
            {
                if($customerGroup == $currentCustomerGroup)
                {
                    $this->_canUseCheckout = true;
                    break;
                }
            }
        }
    }
}