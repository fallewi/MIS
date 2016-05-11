<?php
/**
 * BlueAcorn_ProductVideos extension
 * 
 *
 * @category       BlueAcorn
 * @package        BlueAcorn_ProductVideos
 * @author         Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright      Copyright © 2014 Blue Acorn, Inc.
 */
/**
 * Product Video file field renderer helper
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Block_Adminhtml_Video_Helper_File
    extends Varien_Data_Form_Element_Abstract {
    /**
     * constructor
     * @access public
     * @param array $data
     *
     */
    public function __construct($data){
        parent::__construct($data);
        $this->setType('file');
    }
    /**
     * get element html
     * @access public
     * @return string
     *
     */
    public function getElementHtml(){
        $html = '';
        $this->addClass('input-file');
        $html.= parent::getElementHtml();
        if ($this->getValue()) {
            $url = $this->_getUrl();
            if( !preg_match("/^http\:\/\/|https\:\/\//", $url) ) {
                if($this->getId()== 'thumbnail'){
                    $url = Mage::helper('blueacorn_productvideos/video')->getThumbBaseUrl() . $url;
                }
                else{
                    $url = Mage::helper('blueacorn_productvideos/video')->getFileBaseUrl() . $url;
                }

            }
            $html .= '<br /><a href="'.$url.'">'.$this->_getUrl().'</a> ';
        }
        $html.= $this->_getDeleteCheckbox();
        return $html;
    }
    /**
     * get the delete checkbox HTML
     * @access protected
     * @return string
     *
     */
    protected function _getDeleteCheckbox(){
        $html = '';
        if ($this->getValue()) {
            $label = Mage::helper('blueacorn_productvideos')->__('Delete File');
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox" name="'.parent::getName().'[delete]" value="1" class="checkbox" id="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' disabled="disabled"': '').'/>';
            $html .= '<label for="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' class="disabled"' : '').'> '.$label.'</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }
        return $html;
    }
    /**
     * get the hidden input
     * @access protected
     * @return string
     *
     */
    protected function _getHiddenInput(){
        return '<input type="hidden" name="'.parent::getName().'[value]" value="'.$this->getValue().'" />';
    }
    /**
     * get the file url
     * @access protected
     * @return string
     *
     */
    protected function _getUrl(){
        return $this->getValue();
    }
    /**
     * get the name
     * @access public
     * @return string
     *
     */
    public function getName(){
        return $this->getData('name');
    }
}
