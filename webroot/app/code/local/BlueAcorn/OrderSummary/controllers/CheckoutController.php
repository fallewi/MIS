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
	}