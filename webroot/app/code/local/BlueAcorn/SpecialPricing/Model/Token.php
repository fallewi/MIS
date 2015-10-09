<?php
/**
 * BlueAcorn_SpecialPricing extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       BlueAcorn
 * @package        BlueAcorn_SpecialPricing
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Token model
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_SpecialPricing
 * @author      Ultimate Module Creator
 */
class BlueAcorn_SpecialPricing_Model_Token extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'blueacorn_specialpricing_token';
    const CACHE_TAG = 'blueacorn_specialpricing_token';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'blueacorn_specialpricing_token';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'token';

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('blueacorn_specialpricing/token');
    }

    /**
     * before save token
     *
     * @access protected
     * @return BlueAcorn_SpecialPricing_Model_Token
     * @author Ultimate Module Creator
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * save token relation
     *
     * @access public
     * @return BlueAcorn_SpecialPricing_Model_Token
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}
