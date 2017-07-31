<?php

/**
 * Cminds MultiUserAccounts subaccount controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotrek.pierzak@gmail.com>
 * @license  https://www.cminds.com/cm-magento-extentions-license-details CreativeMinds Magento Extensions License
 * @link     https://www.cminds.com/ecommerce-extensions-store/magento-multi-user-account-extension
 */
class Cminds_MultiUserAccounts_SubaccountController
    extends Mage_Core_Controller_Front_Action
{
    /**
     * Emulate action.
     *
     * @return void
     */
    public function emulateAction()
    {
        $subaccountId = (int)$this->getRequest()->getParam('id');
        if (!$subaccountId) {
            $this->_redirect('/');
        }

        $emulateModel = Mage::getModel(
            'cminds_multiuseraccounts/subAccount_emulate'
        );

        try {
            $emulateModel
                ->setSubaccountId($subaccountId)
                ->emulate();

            $this->_redirect('customer/account');
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addError(
                $e->getMessage()
            );
            $this->_redirect('customer/account/subAccount');
        }
    }

    /**
     * Restore action.
     *
     * @return void
     */
    public function restoreAction()
    {
        $emulateModel = Mage::getModel(
            'cminds_multiuseraccounts/subAccount_emulate'
        );

        try {
            $emulateModel->restore();

            $this->_redirect('customer/account/subAccount');
        } catch (Mage_Core_Exception $e) {
            $dataHelper = Mage::helper('cminds_multiuseraccounts');

            Mage::getSingleton('core/session')->addError(
                $dataHelper->__(
                    'Something goes wrong during customer session restoration.'
                )
            );

            $this->_redirect('customer/account');
        }
    }

    public function checkBalanceAction()
    {
        $subAccountId = $this->getRequest()->getParam('id');
        $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($subAccountId);
        $period = Mage::getModel('cminds_multiuseraccounts/subAccount_limits')
            ->getOptionText($subAccount->getOrderAmountLimit());

        $amount = $subAccount->getOrderAmountByDate($period);
        $jsonData = json_encode(Mage::helper('core')->currency($amount, true, false) . ' per ' . $period);

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}
