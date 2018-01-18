<?php
	/**
	 * @package     BlueAcorn_OrderSummary
	 * @version     1.0.0
	 * @author      Blue Acorn, Inc. <code@blueacorn.com>
	 * @copyright   Copyright © 2018 Blue Acorn, Inc.
	 */
	class BlueAcorn_OrderSummary_Block_Summary extends Mage_Checkout_Block_Cart_Abstract {
		
		/**
		 * @return array
		 */
		public function getItems() {
			return Mage::getSingleton('checkout/cart')->getQuote()->getAllVisibleItems();
		}
		
		
		/**
		 * @return string
		 */
		public function getGrandTotal() {
			return Mage::helper('core')->currency(Mage::getModel('checkout/session')->getQuote()->getData('grand_total'), true, false);
		}
	}