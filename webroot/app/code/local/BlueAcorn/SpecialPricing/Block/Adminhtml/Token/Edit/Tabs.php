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
 * Token admin edit tabs
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_SpecialPricing
 * @author      Ultimate Module Creator
 */
class BlueAcorn_SpecialPricing_Block_Adminhtml_Token_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('token_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('blueacorn_specialpricing')->__('Token'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return BlueAcorn_SpecialPricing_Block_Adminhtml_Token_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_token',
            array(
                'label'   => Mage::helper('blueacorn_specialpricing')->__('Token'),
                'title'   => Mage::helper('blueacorn_specialpricing')->__('Token'),
                'content' => $this->getLayout()->createBlock(
                    'blueacorn_specialpricing/adminhtml_token_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_token',
                array(
                    'label'   => Mage::helper('blueacorn_specialpricing')->__('Store views'),
                    'title'   => Mage::helper('blueacorn_specialpricing')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'blueacorn_specialpricing/adminhtml_token_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve token entity
     *
     * @access public
     * @return BlueAcorn_SpecialPricing_Model_Token
     * @author Ultimate Module Creator
     */
    public function getToken()
    {
        return Mage::registry('current_token');
    }
}
