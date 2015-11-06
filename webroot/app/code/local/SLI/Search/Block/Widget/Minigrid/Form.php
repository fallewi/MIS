<?php
/**
 * Copyright (c) 2013 S.L.I. Systems, Inc. (www.sli-systems.com) - All Rights Reserved
 * This file is part of Learning Search Connect.
 * Learning Search Connect is distribute under license,
 * go to www.sli-systems.com/LSC for full license details.
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 * 
 * Form that utilizes a minigrid. Good as an alternative
 * to a grid
 *
 * NOTE: Not used with SLI Search
 * 
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Block_Widget_Minigrid_Form extends Mage_Adminhtml_Block_Abstract {
    
    protected $_formId = "ba-minigrid-form";
    protected $_formClass = "";
    protected $_formAction = "";
    protected $_gridFields = array();
    protected $_gridRowData = array();
    
    /**
     * It was decided to use the overwritable _toHtml instead of a template
     * to be consistent with magento renderers and to increase portability
     * 
     * @return string
     */
    protected function _toHtml() {
        $html = parent::_toHtml();
        $html .= "<form id='{$this->getFormId()}' action='{$this->getFormAction()}' method='post' class='{$this->getFormClass()}' enctype='multipart/form-data'>";
        $minigrid = new SLI_Search_Block_System_Config_Form_Field_Minigrid();
        $html .= $minigrid->getElementHtml("ba-minigrid-form-grid", $this->getFieldName(), $this->getGridFields(), $this->getGridRowData());
        $html .= "
            </form>
            <script type='text/javascript'>
                function submitMinigridForm() {
                    $('{$this->getFormId()}').submit();
                }
            </script>

";
        return $html;
    }
    
    
    /**
     * Public return for form id
     * 
     * @return string 
     */
    public function getFormId() {
        return $this->_formId;
    }
    
    /**
     * Public set for form id
     *
     * @param string $formId
     * @return Blueacorn_Minigrid_Block_Widget_Minigrid_Form 
     */
    public function setFormId($formId) {
        $this->_formId = $formId;
        return $this;
    }
    
    /**
     * Public return for form class
     * 
     * @return string
     */
    public function getFormClass() {
        return $this->_formClass;
    }
    
    /**
     * Public set for form class
     *
     * @param string $formClass
     * @return Blueacorn_Minigrid_Block_Widget_Minigrid_Form 
     */
    public function setFormClass($formClass) {
        $this->_formClass = $formClass;
        return $this;
    }
    /**
     * Public return for form class
     * 
     * @return string
     */
    public function getFormAction() {
        return $this->_formAction;
    }
    
    /**
     * Public set for form class
     *
     * @param string $url
     * @return Blueacorn_Minigrid_Block_Widget_Minigrid_Form 
     */
    public function setFormAction($url) {
        $this->_formAction = $url;
        return $this;
    }
    
    /**
     * Public return for grid fields
     * 
     * @return string
     */
    public function getGridFields() {
        return $this->_gridFields;
    }
    
    /**
     * Public set for grid fields
     *
     * @param array $gridFields
     * @return Blueacorn_Minigrid_Block_Widget_Minigrid_Form 
     */
    public function setGridFields($gridFields) {
        $this->_gridFields = $gridFields;
        return $this;
    }
    
    /**
     * Public return for grid row data
     * 
     * @return string
     */
    public function getGridRowData() {
        return $this->_gridRowData;
    }
    
    /**
     * Public set for grid row data
     *
     * @param array $gridRowData
     * @return Blueacorn_Minigrid_Block_Widget_Minigrid_Form 
     */
    public function setGridRowData($gridRowData) {
        $this->_gridRowData = $gridRowData;
        return $this;
    }
  
}