<?php
/**
* @package     BlueAcorn\CmsStyleUpdate
* @version     1.0.1
* @author      Blue Acorn, Inc. <code@blueacorn.com>
* @copyright   Copyright © 2014 Blue Acorn, Inc.
*/

class BlueAcorn_CmsStyleUpdate_Model_Observer
{

    /**
     * Making changes to design tab regarding to custom logic
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Cms_Model_Observer
     */
    public function onDesignTabPrepareForm($observer)
    {
        $form = $observer->getEvent()->getForm();

        $fieldset = $form->addFieldset(
            'blueacorn', array(
                'legend' => 'Styles',
                'class' => 'fieldset-wide'
            )
        );

        $fieldset->addField('custom_style_update_css', 'textarea', array(
            'name'  => 'custom_style_update_css',
            'label' => Mage::helper('cms')->__('Custom Layout Style CSS'),
            'style' => 'height:24em;',
            'note'      => Mage::helper('cms')->__('Do not add style tags'),
        ));
    }

}