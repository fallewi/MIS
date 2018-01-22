<?php
	/**
	 * @package     BlueAcorn_OrderSummary
	 * @version     1.0.0
	 * @author      Blue Acorn, Inc. <code@blueacorn.com>
	 * @copyright   Copyright © 2018 Blue Acorn, Inc.
	 */
	class BlueAcorn_OrderSummary_Block_Totals extends Mage_Checkout_Block_Cart_Abstract {
		
		/**
		 * @return string
		 */
		public function getGrandTotal() {
			$__shippingAmount = $this->__getQuote()->getShippingAddress()->getShippingAmount();
			if ( $this->getIsKnown() ) {
				$__shippingAmount = 0;
			}
			
			return Mage::helper('core')->currency($this->__getQuote()->getData('grand_total') - $__shippingAmount, true, false);
		}
		
		
		/**
		 * @return string
		 */
		public function getSubtotal() {
			return Mage::helper('core')->currency($this->__getQuote()->getData('subtotal'), true, false);
		}
		
		
		/**
		 * @return string
		 */
		public function getShippingAmount() {
			$__result = '-';
			if ( $this->getIsKnown() ) {
				$__amount = $this->__getQuote()->getShippingAddress()->getShippingAmount();
				if ( $this->__getQuote()->getShippingAddress()->getShippingMethod() ) {
					$__result = Mage::helper('core')->currency($__amount, true, false);
				}
			}
			
			return $__result;
		}
		
		
		/**
		 *
		 */
		protected function __getQuote() {
			return Mage::getModel('checkout/session')->getQuote();
		}
		
		
		/**
		 *
		 */
		protected function _prepareLayout() {
			if ( !$this->getTemplate() ) {
				$this->setTemplate('blueacorn/ordersummary/totals.phtml');
			}
			
			return parent::_prepareLayout();
		}
	}