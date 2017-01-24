<?php
/**
 * @package     BlueAcorn\CsvExport
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2017 Blue Acorn, Inc.
 */

class BlueAcorn_CsvExport_Block_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**Builds button for admin to run cron manually
     * @param Varien_Data_Form_Element_Abstract $element
     * @return mixed
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('*/marketing/index');
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Run Marketing CSV Now!')
            ->setOnClick("setLocation('$url')")
            ->toHtml();
        return $html;
    }
}