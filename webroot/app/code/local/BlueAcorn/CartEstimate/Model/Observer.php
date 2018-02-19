<?php
	/**
	 * @package     BlueAcorn_CartEstimate
	 * @version     1.0.0
	 * @author      Blue Acorn, Inc. <code@blueacorn.com>
	 * @copyright   Copyright © 2018 Blue Acorn, Inc.
	 */
	class BlueAcorn_CartEstimate_Model_Observer {
		
		/**
		 * @param Varien_Event_Observer $observer
		 *
		 * @return BlueAcorn_CartEstimate_Model_Observer $this
		 */
		public function processEstimateAddress(Varien_Event_Observer $observer) {
			$__quote = Mage::getSingleton('checkout/cart')->getQuote();
			if ( $__quote->getItemsCount() and (!$__quote->getShippingAddress()->getPostcode() or !$__quote->getShippingAddress()->getRegionId()) ) {
				$__helper = Mage::helper('blueacorn_cartestimate');
				if ( $__helper->getIsEnabled() ) {
					$__token = $__helper->getAccessToken();
					if ( $__token ) {
						$__userIp = Mage::helper('core/http')->getRemoteAddr();
						if ( $__userIp ) {
							$__url = "http://ipinfo.io/" . $__userIp . "/json?token=" . $__token;
							$__ch = curl_init($__url);
							curl_setopt($__ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($__ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0');
							$__response = curl_exec($__ch);
							curl_close($__ch);
							if ( $__response ) {
								try {
									$__address = json_decode($__response, true);
									if ( array_key_exists('country', $__address) and $__address['country'] == 'US' ) {
										if ( array_key_exists('postal', $__address) and $__address['postal'] and array_key_exists('region', $__address) and $__address['region'] ) {
											$__regionId = false;
											$__regionCollection = Mage::getModel('directory/region_api')->items('US');
											foreach ( $__regionCollection as $__region ) {
												if ( $__region['name'] == $__address['region'] ) {
													$__regionId = $__region['region_id'];
													break;
												}
											}
											if ( $__regionId ) {
												$__cart = Mage::getSingleton('checkout/cart');
												$__cart->init();
												$__cart->save();
												$__cart->getQuote()->getShippingAddress()
													->setCountryId('US')
													->setCity( array_key_exists('city', $__address) ? $__address['city'] : '' )
													->setPostcode($__address['postal'])
													->setRegionId($__regionId)
													->setRegion($__address['region'])
													->setCollectShippingRates(true)
												;
												$__cart->getQuote()->save();
												
												
												$__cart->getQuote()->getShippingAddress()->setCollectShippingRates(true);
												$__cart->getQuote()->collectTotals();
												$__cart->getQuote()->save();
												
												$__cart->save();
												
												$__cart->getQuote()->getShippingAddress()->collectShippingRates()->getGroupedAllShippingRates();
											}
										}
									}
								}
								catch ( Exception $__e ) {
									Mage::logException($__e);
								}
							}
						}
					}
				}
			}
		}
	}