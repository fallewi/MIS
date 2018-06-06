<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_List
 */    
class Amasty_List_Adminhtml_Amlist_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        Mage::register('current_customer_id', (int) $this->getRequest()->getParam('customer_id'));
        $this->getResponse()->setBody($this->getLayout()->createBlock('amlist/adminhtml_customer_edit_tab')->toHtml());
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/manage');
    }
}