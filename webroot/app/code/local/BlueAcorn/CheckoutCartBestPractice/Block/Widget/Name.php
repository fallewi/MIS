<?php
/**
* @package     BlueAcorn\CheckoutCartBestPractice
* @version     0.1.0
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2015 Blue Acorn, Inc.
*/

class BlueAcorn_CheckoutCartBestPractice_Block_Widget_Name extends Mage_Customer_Block_Widget_Name
{
    public function _construct()
    {
        parent::_construct();

        // default template location
        $this->setTemplate('blueacorn/customer/widget/name.phtml');
    }
}