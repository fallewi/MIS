<?php

/**
 * Levementum_Customer setup script
 *
 * @category   Levementum
 * @package    Levementum_Customer
 * @author 	   dbeljic@westum.com
 */

$installer = $this;
$installer->startSetup();

$installer->addAttribute('customer', 'assigned_salesperson', array(
    'type'                         => 'int',
    'label'                        => 'Assigned Salesperson',
    'input'                        => 'select',
    'source'                       => 'levementum_customer/customer_attribute_source_salesperson',
    /*'backend'                      => 'levementum_customer/customer_attribute_backend_salesperson', */
    'visible_on_front'             => 0,
    'required' => 0,
    'user_defined' => 1,
    'default' => ''

));

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'assigned_salesperson');
$attribute->setData('used_in_forms' , array(
    'adminhtml_customer','customer_account_edit', 'adminhtml_checkout'
));

$attribute->save();

$installer->endSetup();


?>
