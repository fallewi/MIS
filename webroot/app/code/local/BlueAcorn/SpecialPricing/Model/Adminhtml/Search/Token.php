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
 * Admin search model
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_SpecialPricing
 * @author      Ultimate Module Creator
 */
class BlueAcorn_SpecialPricing_Model_Adminhtml_Search_Token extends Varien_Object
{
    /**
     * Load search results
     *
     * @access public
     * @return BlueAcorn_SpecialPricing_Model_Adminhtml_Search_Token
     * @author Ultimate Module Creator
     */
    public function load()
    {
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('blueacorn_specialpricing/token_collection')
            ->addFieldToFilter('token', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $token) {
            $arr[] = array(
                'id'          => 'token/1/'.$token->getId(),
                'type'        => Mage::helper('blueacorn_specialpricing')->__('Token'),
                'name'        => $token->getToken(),
                'description' => $token->getToken(),
                'url' => Mage::helper('adminhtml')->getUrl(
                    '*/specialpricing_token/edit',
                    array('id'=>$token->getId())
                ),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
