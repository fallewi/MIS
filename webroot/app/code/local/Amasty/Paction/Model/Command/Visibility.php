<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
class Amasty_Paction_Model_Command_Visibility extends Amasty_Paction_Model_Command_Abstract
{ 
    public function __construct($type)
    {
        parent::__construct($type);
        $this->_label = 'Change Visibility';
    }
    
    /**
     * Executes the command
     *
     * @param array $ids product ids
     * @param int $storeId store id
     * @param string $val field value
     * @throws Exception
     * @return string success message if any
     */    
    public function execute($ids, $storeId, $val)
    {
        $success = parent::execute($ids, $storeId, $val);
        $data['visibility'] = $val;
        Mage::getSingleton('catalog/product_action')->updateAttributes($ids, $data, $storeId);
        $success = Mage::helper('ampaction')->__('Total of %d product(s) were updated.', count($ids));
        return $success;
    }
    
    /**
     * Returns value field options for the mass actions block
     *
     * @param string $title field title
     * @return array
     */
    protected function _getValueField($title)
    {
        $field = parent::_getValueField($title);
        
        $options = Mage::getModel('catalog/product_visibility')->getAllOptions();
        unset($options[0]);
        $field['ampaction_value']['label']  = Mage::helper('ampaction')->__('To');
        $field['ampaction_value']['type']   = 'select';
        $field['ampaction_value']['values'] = $options;
        
        return $field;
    }
}