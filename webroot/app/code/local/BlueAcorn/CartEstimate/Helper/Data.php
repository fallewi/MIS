<?php
	/**
	 * @package     BlueAcorn_CartEstimate
	 * @version     1.0.0
	 * @author      Blue Acorn, Inc. <code@blueacorn.com>
	 * @copyright   Copyright © 2018 Blue Acorn, Inc.
	 */
	class BlueAcorn_CartEstimate_Helper_Data extends Mage_Core_Helper_Abstract {
		
		/**
		 * Constants
		 */
		const XML_PATH_ESTIMATION_ENABLED = 'blueacorn_cartestimate/general/enabled';
		const XML_PATH_ACCESS_TOKEN       = 'blueacorn_cartestimate/general/access_token';
		
		
		/**
		 * @return string
		 */
		public function getIsEnabled() {
		    return Mage::getStoreConfig(self::XML_PATH_ESTIMATION_ENABLED);
		}
		
		
		/**
		 * @return string
		 */
		public function getAccessToken() {
		    return Mage::getStoreConfig(self::XML_PATH_ACCESS_TOKEN);
		}
	}