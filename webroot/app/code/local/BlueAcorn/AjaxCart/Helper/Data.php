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
		 *
		 */
		public function getNotificationDuration() {
			$__duration = Mage::getStoreConfig(self::CFG_PATH_NOTIFICATION_DURATION);
			if ( !$__duration or $__duration < 1 ) {
				$__duration = self::DEFAULT_DURATION;
			}
			
			return (int) $__duration;
		}
	}