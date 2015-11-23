<?php
/**
 * ${PROJECT_NAME} - ${FILE_NAME}
 * Created by JetBrains PhpStorm.
 * User: Russell - rmantilla@levementum.com
 * Date: 11/12/13
 * Time: 10:45 AM
 */ 
class Levementum_AdminOrders_Block_Adminhtml_Sales_Order_Create_Customer_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Customer_Grid
{
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    =>Mage::helper('sales')->__('Name'),
            'index'     =>'name'
        ));
        $this->addColumn('billing_company', array(
            'header'    =>Mage::helper('sales')->__('Company Name'),
            'index'     =>'billing_company'
        ));
        $this->addColumn('email', array(
            'header'    =>Mage::helper('sales')->__('Email'),
            'width'     =>'150px',
            'index'     =>'email'
        ));
        $this->addColumn('Telephone', array(
            'header'    =>Mage::helper('sales')->__('Telephone'),
            'width'     =>'100px',
            'index'     =>'billing_telephone'
        ));
        $this->addColumn('billing_postcode', array(
            'header'    =>Mage::helper('sales')->__('ZIP/Post Code'),
            'width'     =>'120px',
            'index'     =>'billing_postcode',
        ));
        $this->addColumn('billing_regione', array(
            'header'    =>Mage::helper('sales')->__('State/Province'),
            'width'     =>'100px',
            'index'     =>'billing_regione',
        ));
    }
}