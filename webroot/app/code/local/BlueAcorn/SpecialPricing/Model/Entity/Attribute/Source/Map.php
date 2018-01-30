<?php
/**
 * @package     BlueAcorn\SpecialPricing
 * @version     1.0.0
 * @author      Sam Tay @ Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2016 Blue Acorn, Inc.
 */
class BlueAcorn_SpecialPricing_Model_Entity_Attribute_Source_Map
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const DISABLED = 0;
    const EMAIL = 1;
    const CALL = 2;
    const NOADDPRICE = 3;
    const NOPRICEGUEST = 4;

    /**
     * Get all attribute options
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array('value' => self::DISABLED, 'label' => 'Disabled'),
                array('value' => self::EMAIL, 'label' => 'Requires MAP Email'),
                array('value' => self::CALL, 'label' => 'Requires MAP Call'),
                array('value' => self::NOADDPRICE, 'label' => 'No Price No Add to Cart'),
                array('value' => self::NOPRICEGUEST, 'label' => 'No Price Guest User')
            );
        }
        return $this->_options;
    }
}
