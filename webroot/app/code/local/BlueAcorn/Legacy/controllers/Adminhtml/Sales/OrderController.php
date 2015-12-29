<?php

require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';

class BlueAcorn_Legacy_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Orders'));

        $this->_initAction();

        $this->getLayout()->getBlock('content')->insert($this->getLayout()->createBlock('cms/block')->setBlockId('legacy_orders'), '', false);

        $this->renderLayout();
    }
}