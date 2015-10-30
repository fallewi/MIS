<?php
/**
 * ${PROJECT_NAME} - ${FILE_NAME}
 * Created by JetBrains PhpStorm.
 * User: Russell - rmantilla@levementum.com
 * Date: 11/12/13
 * Time: 10:45 AM
 */ 
class Levementum_AdminOrders_Block_Adminhtml_Sales_Order_Create_Customer_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Customer_Grid {

    //added field via mage core hack
//    protected function _prepareCollection()
//    {
//
//        parent::_prepareCollection();
//
//        $collection = $this->getCollection();
////       // $collection = Mage::getResourceModel('customer/customer_collection');
////
//        $collection
//            ->joinAttribute('billing_company', 'customer_address/company', 'default_billing', null, 'left')
//;
//        $this->setCollection($collection);
//        echo $collection->getSelectSql(true);
//
//        return $collection;
//
//    }


    protected function _prepareColumns()
    {
//        $this->addColumn('entity_id', array(
//            'header'    =>Mage::helper('sales')->__('ID'),
//            'width'     =>'50px',
//            'index'     =>'entity_id',
//            'align'     => 'right',
//        ));
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
//        $this->addColumn('billing_country_id', array(
//            'header'    =>Mage::helper('sales')->__('Country'),
//            'width'     =>'100px',
//            'type'      =>'country',
//            'index'     =>'billing_country_id',
//        ));
        $this->addColumn('billing_regione', array(
            'header'    =>Mage::helper('sales')->__('State/Province'),
            'width'     =>'100px',
            'index'     =>'billing_regione',
        ));

//        $this->addColumn('store_name', array(
//            'header'    =>Mage::helper('sales')->__('Signed Up From'),
//            'align'     => 'center',
//            'index'     =>'store_name',
//            'width'     =>'130px',
//        ));

  //      return parent::_prepareColumns();
    }
}