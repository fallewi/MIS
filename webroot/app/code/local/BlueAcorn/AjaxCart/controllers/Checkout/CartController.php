<?php
	/**
	 * @package     BlueAcorn_AjaxCart
	 * @version
	 * @author      Blue Acorn, Inc. <code@blueacorn.com>
	 * @copyright   Copyright © 2017 Blue Acorn, Inc.
	 */
	require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'CartController.php';
	
	class BlueAcorn_AjaxCart_Checkout_CartController extends Mage_Checkout_CartController {
		
		protected $_errorMessage = false;
		protected $_addAction;
		protected $_product;
		
		
		/**
		 * Add product to shopping cart
		 *
		 * @return $this
		 */
		public function addAction() {
			$this->_addAction = true;
			
			if ( !$this->_validateFormKey() ) {
				$this->_errorMessage = $this->__('Form key validation failed. Please refresh page.');
				$this->_goBack();
				return;
			}
			
			$__cart   = $this->_getCart();
			$__params = $this->getRequest()->getParams();
			try {
				if ( isset($__params['qty']) ) {
					$__filter = new Zend_Filter_LocalizedToNormalized(
						array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$__params['qty'] = $__filter->filter($__params['qty']);
				}
				
				$this->_product = $this->_initProduct();
				$__related = $this->getRequest()->getParam('related_product');
				
				if ( !$this->_product || $this->_product->isDisabled() ) {
					$this->_errorMessage = $this->__('The product is not found.');
					$this->_goBack();
					return;
				}
				
				$__cart->addProduct($this->_product, $__params);
				if ( !empty($__related) ) {
					$__cart->addProductsByIds(explode(',', $__related));
				}
				
				$__cart->save();
				$this->_getSession()->setCartWasUpdated(true);
				
				Mage::dispatchEvent(
					'checkout_cart_add_product_complete',
					array(
						'product' => $this->_product,
						'request' => $this->getRequest(),
						'response' => $this->getResponse()
					)
				);
				
				$this->_errorMessage = false;
				$this->_goBack();
			}
			catch ( Mage_Core_Exception $e ) {
				$this->_errorMessage = Mage::helper('core')->escapeHtml($e->getMessage());
				$this->_goBack();
			}
			catch ( Exception $e ) {
				Mage::logException($e);
				
				$this->_errorMessage = $this->__('Failed to add product to shopping cart.');
				$this->_goBack();
			}
		}
		
		
		/**
		 * Remove product from shopping cart
		 *
		 * @return $this
		 */
		public function removeAction() {
			$this->_addAction = false;
			
			if ( !$this->_validateFormKey() ) {
				$this->_errorMessage = $this->__('Form key validation failed. Please refresh page.');
				$this->_goBack();
				return;
			}
			
			$__cart   = $this->_getCart();
			
			try {
				$this->_product = $this->_initProduct();
				
				if ( !$this->_product or !$this->_product->getId() ) {
					$this->_errorMessage = $this->__('The product is not found.');
					$this->_goBack();
					return;
				}
				
				$__removed = false;
				foreach ( $__cart->getItems() as $__item ) {
					if ( $__item->getProduct()->getId() == $this->_product->getId() ) {
						$__itemId = $__item->getItemId();
						$__cart->removeItem($__itemId)->save();
						$this->_getSession()->setCartWasUpdated(true);
						$__removed = true;
						
						break;
					}
				}
				if ( !$__removed ) {
					$this->_errorMessage = $this->__('Failed to find this product in cart.');
				}
				
				$this->_errorMessage = false;
				$this->_goBack();
			}
			catch ( Mage_Core_Exception $e ) {
				$this->_errorMessage = Mage::helper('core')->escapeHtml($e->getMessage());
				$this->_goBack();
			}
			catch ( Exception $e ) {
				Mage::logException($e);
				
				$this->_errorMessage = $this->__('Failed to remove product from shopping cart.');
				$this->_goBack();
			}
		}
		
		
		/**
		 * Send Ajax Response
		 *
		 * @return $this
		 */
		protected function _goBack() {
			$__ajaxResponse = [];
			if ( !$this->getRequest()->isXmlHttpRequest() ) {
				parent::_goBack();
			}
			else {
				$__duration = Mage::helper('blueacorn_ajaxcart')->getNotificationDuration() * 1000;
				if ( $this->_errorMessage ) {
					$__ajaxResponse = [
						'success'  => false,
						'message'  => $this->_errorMessage,
						'duration' => $__duration
					];
				}
				else {
					$this->loadLayout();
					$__ajaxResponse = [
						'success'  => true,
						'duration' => $__duration,
						'qty'      => $this->_getCart()->getSummaryQty(),
						'SID'      => Mage::getSingleton('core/session')->getSessionId(),
						'carthtml' => Mage::app()->getLayout()->getBlock('minicart_content')->toHtml(),
						'total'    => Mage::helper('checkout')->formatPrice($this->_getQuote()->getGrandTotal())
					];
					if ( $this->_addAction ) {
						$__ajaxResponse['message'] = $this->__('%s was added to your shopping cart.', $this->_product->getName());
					}
					else {
						$__ajaxResponse['message'] = $this->__('%s was removed from your shopping cart.', $this->_product->getName());
					}
				}
				
				$this->getResponse()->setBody(Zend_Json::encode($__ajaxResponse));
			}
			
			return $this;
		}
	}