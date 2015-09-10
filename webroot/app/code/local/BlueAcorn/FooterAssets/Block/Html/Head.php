<?php

/**
 * @package     BlueAcorn\FooterAssets
 * @version 	V 0.1.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2014 Blue Acorn, Inc.
 */

class BlueAcorn_FooterAssets_Block_Html_Head extends Mage_Page_Block_Html_Head
{

	protected function _construct() 
	{
        $this->setTemplate('blueacorn/footer_assets.phtml');
	$this->setData('is_enterprise', Mage::getEdition() == Mage::EDITION_ENTERPRISE);
	$this->_data['items'] = array();
	}


}
