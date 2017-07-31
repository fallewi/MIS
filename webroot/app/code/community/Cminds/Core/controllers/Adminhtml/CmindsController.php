<?php

class Cminds_Core_Adminhtml_CmindsController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/customer');
    }

    public function deactivateLicenseAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        $id = str_replace('_is_approved', '', $id);
        $id = str_replace('row_cmindsConf_', '', $id);

        if ($id) {
            Mage::getModel('cminds/deactivate')->run($id);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('success' => true)));
        } else {
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('success' => false)));
        }
    }
}