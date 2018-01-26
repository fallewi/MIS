<?php
	/**
	 * @package     BlueAcorn_OrderSummary
	 * @version     1.0.0
	 * @author      Blue Acorn, Inc. <code@blueacorn.com>
	 * @copyright   Copyright © 2018 Blue Acorn, Inc.
	 */
	class BlueAcorn_OrderSummary_CheckoutController extends Mage_Core_Controller_Front_Action {
		
		/**
		 *
		 */
		public function totalsAction() {
			$this->loadLayout();
			$__block = $this->getLayout()->createBlock('blueacorn_ordersummary/totals')->setIsKnown(true);
			
			$this->getResponse()->setBody($__block->toHtml());
		}
		
		
		/**
		 * Initialize coupon
		 */
		public function couponPostAction() {
			/**
			 * No reason continue with empty shopping cart
			 */
			if ( !$this->_getCart()->getQuote()->getItemsCount() ) {
				$this->_redirect('checkout/onepage');
				return;
			}
			
			$couponCode = (string) $this->getRequest()->getParam('coupon_code');
			if ( $this->getRequest()->getParam('remove') == 1 ) {
				$couponCode = '';
			}
			$oldCouponCode = $this->_getQuote()->getCouponCode();
			
			if (!strlen($couponCode) && !strlen($oldCouponCode)) {
				$this->_redirect('checkout/onepage');
				return;
			}
			
			try {
				$codeLength = strlen($couponCode);
				$isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;
				
				$this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
				$this->_getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')
					->collectTotals()
					->save()
				;
				
				if ( $codeLength ) {
					if ( $isCodeLengthValid && $couponCode == $this->_getQuote()->getCouponCode() ) {
						$this->_getSession()->addSuccess(
							$this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode))
						);
					}
					else {
						$this->_getSession()->addError(
							$this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode))
						);
					}
				} else {
					$this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
				}
			}
			catch ( Mage_Core_Exception $e ) {
				$this->_getSession()->addError($e->getMessage());
			}
			catch ( Exception $e ) {
				$this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
				Mage::logException($e);
			}
			
			$this->_redirect('checkout/onepage');
		}
		
		
		/**
		 * Retrieve shopping cart model object
		 *
		 * @return Mage_Checkout_Model_Cart
		 */
		protected function _getCart() {
			return Mage::getSingleton('checkout/cart');
		}
		
		/**
		 * Get checkout session model instance
		 *
		 * @return Mage_Checkout_Model_Session
		 */
		protected function _getSession() {
			return Mage::getSingleton('checkout/session');
		}
		
		/**
		 * Get current active quote instance
		 *
		 * @return Mage_Sales_Model_Quote
		 */
		protected function _getQuote() {
			return $this->_getCart()->getQuote();
		}
	}