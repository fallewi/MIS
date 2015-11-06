<?php
/**
 * @package     BlueAcorn\CacheManagementMods
 * @version
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */
class BlueAcorn_CacheManagementMods_Block_CommandList extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('label', array(
            'label' => Mage::helper('adminhtml')->__('Label'),
            'style' => 'width:120px',
        ));
        $this->addColumn('description', array(
            'label' => Mage::helper('adminhtml')->__('Description'),
            'style' => 'width:300px',
        ));
        $this->addColumn('command', array(
            'label' => Mage::helper('adminhtml')->__('Command'),
            'style' => 'width:120px',
        ));
        $this->addColumn('params', array(
            'label' => Mage::helper('adminhtml')->__('Parameters'),
            'style' => 'width:120px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Button');
        parent::__construct();
    }
}
