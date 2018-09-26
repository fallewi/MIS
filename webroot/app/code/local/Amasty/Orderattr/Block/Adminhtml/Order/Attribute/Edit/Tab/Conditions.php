<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class Amasty_Orderattr_Block_Adminhtml_Order_Attribute_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form
{
    public function getActiveShippingMethods()
    {
        $methods = array();

        $activeCarriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
        foreach($activeCarriers as $carrierCode => $carrierModel)
        {
           $options = array();
           $carrierTitle = '';
           if( $carrierMethods = $carrierModel->getAllowedMethods() )
           {
               foreach ($carrierMethods as $methodCode => $method)
               {
                    $code= $carrierCode.'_'.$methodCode;
                    $options[]=array('value'=>$code,'label'=>$method);

               }
               $carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title');

           }
            $methods[]=array('value'=>$options,'label'=>$carrierTitle);
        }
        return $methods;
    }
    
    protected function _prepareForm()
    {
        $model = Mage::registry('entity_attribute');
        
        $currentShippingMethods = Mage::helper('amorderattr')->getShippingMethods($model->getId());
        $formData = array();
        
        foreach($currentShippingMethods as $method){
            $formData[] = $method->getShippingMethod();
        }
        
        
        $form = new Varien_Data_Form(array(
            'id' => 'conditions_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));
        
        $fieldset = $form->addFieldset('conditions_fieldset',
            array('legend' => Mage::helper('amorderattr')->__('Manage Conditions'))
        );
        
        $methods = $this->getActiveShippingMethods();

        $fieldset->addField('shipping_methods', 'multiselect', array(
            'name'      => 'shipping_methods[]',
            'label'     => Mage::helper('amorderattr')->__('Shipping Methods'),
            'title'     => Mage::helper('amorderattr')->__('Shipping Methods'),
            'values'    => $methods,
        ));
        
        $form->addValues(array(
            'shipping_methods' => $formData
        ));
        
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
