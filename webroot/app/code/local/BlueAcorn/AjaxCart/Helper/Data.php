<?php
	/**
	 * @package     BlueAcorn_AjaxCart
	 * @version
	 * @author      Blue Acorn, Inc. <code@blueacorn.com>
	 * @copyright   Copyright © 2017 Blue Acorn, Inc.
	 */
	class BlueAcorn_AjaxCart_Helper_Data extends Mage_Core_Helper_Abstract {
		
		const DEFAULT_DURATION               = 5;
		const CFG_PATH_NOTIFICATION_DURATION = 'blueacorn_ajaxcart/general/notification_duration';
		
		
		/**
		 * @return int $__duration
		 */
		public function getNotificationDuration() {
			$__duration = Mage::getStoreConfig(self::CFG_PATH_NOTIFICATION_DURATION);
			if ( !$__duration or $__duration < 1 ) {
				$__duration = self::DEFAULT_DURATION;
			}
			
			return (int) $__duration;
		}
		
		
		/**
		 * @return string $__result
		 */
		public function getContinueShoppingUrl() {
			$__result = Mage::getSingleton('checkout/session')->getData('last_added_product_url');
			if ( !$__result ) {
				$__cartItems = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
				if ( count($__cartItems) ) {
					$__maxId = 0;
					$__lastItem = null;
					foreach ( $__cartItems as $__item ) {
						if ( $__item->getId() > $__maxId ) {
							$__maxId = $__item->getId();
							$__lastItem = $__item;
						}
					}
					if ( $__lastItem ) {
						$__result = $__lastItem->getProduct()->getUrl();
					}
				}
			}
			
			return $__result;
		}
	}