<?php
/**
 * ${PROJECT_NAME} - ${FILE_NAME}
 * Created by JetBrains PhpStorm.
 * User: Russell - rmantilla@levementum.com
 * Date: 11/12/13
 * Time: 11:57 AM
 */

class Levementum_AdminOrders_Block_Adminhtml_Sales_Order_Create_Search_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid {

//rmantilla@levementum - accomplished via codepool hack
//    protected function _prepareCollection()
//    {
//
//        parent::_prepareCollection();
///*        @var $collection Mage_Catalog_Model_Resource_Product_Collection*/
//        $collection = $this->getCollection();
//        $collection->addAttributeToSelect('mpn');
//
//
//
//        $this->setCollection($collection);
//    }


    /**
     * Prepare columns
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareColumns()
    {
//        $this->addColumn('entity_id', array(
//            'header'    => Mage::helper('sales')->__('ID'),
//            'sortable'  => true,
//            'width'     => '60',
//            'index'     => 'entity_id'
//        ));


        $this->addColumn('image', array(
            'header'    => Mage::helper('sales')->__('Product Image'),
            'renderer'  => 'adminorders/adminhtml_sales_order_create_search_grid_renderer_image',
            'index'     => 'image',
            'width'     => '70',
            'filter'    => false
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('sales')->__('Product Name'),
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_product',
            'index'     => 'name'
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('sales')->__('SKU'),
            'width'     => '80',
            'index'     => 'sku'
        ));


        $manCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter(70)
            ->setStoreFilter(0)
            ->load();
        $manufacturers = array();
        foreach ($manCollection as $item) {
            $manufacturers[$item->getId()] = $item->getValue();
        }

        //todo: need to make this column filterable
        $this->addColumn('manufacturer', array(
            'header'    => Mage::helper('sales')->__('Manufacturer'),
            'renderer'  => 'adminorders/adminhtml_sales_order_create_search_grid_renderer_manufacturer',
            'index'     => 'manufacturer',
            'type'      => 'options',
            'options'   => $manufacturers
        ));

        $this->addColumn('mpn', array(
            'header'    => Mage::helper('sales')->__('MPN'),
            'width'     => '80',
            'index'     => 'mpn',
            'type'      => 'text'

        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('sales')->__('Price'),
            'column_css_class' => 'price',
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index'     => 'price',
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_price',
        ));

        $this->addColumn('in_products', array(
            'header'    => Mage::helper('sales')->__('Select'),
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id',
            'sortable'  => false,
        ));

        $this->addColumn('qty', array(
            'filter'    => false,
            'sortable'  => false,
            'header'    => Mage::helper('sales')->__('Qty To Add'),
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_qty',
            'name'      => 'qty',
            'inline_css'=> 'qty',
            'align'     => 'center',
            'type'      => 'input',
            'validate_class' => 'validate-number',
            'index'     => 'qty',
            'width'     => '1',
        ));

     //   return parent::_prepareColumns();
    }

}