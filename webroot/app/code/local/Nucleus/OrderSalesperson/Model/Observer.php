<?php

class Nucleus_OrderSalesperson_Model_Observer {
	public function afterOrderSubmit(Varien_Event_Observer $observer)
	{
		/** @var Mage_Sales_Model_Order $order */
		$order = $observer->getOrder();

		$customer = Mage::getSingleton('customer/session')->getCustomer();

		if ($customer) {
			$salesperson = $customer->getData('assigned_salesperson');
			$order->setAdminId($salesperson)->save();
		} else {
			$order->setAdminId(null)->save();
		}

	}
}