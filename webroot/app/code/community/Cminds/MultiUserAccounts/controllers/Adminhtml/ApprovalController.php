<?php

/**
 * @author CreativeMindsSolutions
 */
class Cminds_MultiUserAccounts_Adminhtml_ApprovalController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/customer');
    }

    public function indexAction()
    {
        $this->_title($this->__('Send to approval carts'));
        $this->loadLayout();
        $this->_setActiveMenu('sales');
        $this->_addContent($this->getLayout()->createBlock('cminds_multiuseraccounts/adminhtml_approval_list'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->_title($this->__('Send to approval carts'));
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cminds_multiuseraccounts/adminhtml_approval_list_grid')->toHtml()
        );
    }
}