<?php

/**
 * @author CreativeMindsSolutions
 */
require_once 'Cminds/MultiUserAccounts/controllers/Adminhtml/SubAccountController.php';
class Cminds_MultiuserSubaccounts_Adminhtml_SubAccountController extends Cminds_MultiUserAccounts_Adminhtml_SubAccountController
{

    public function editPostAction()
    {
        $data = $this->getRequest()->getPost();

        if ($data) {
            // this is not sent by Post but in the url
            $subAccountId = $this->getRequest()->getParam('id');
            $subAccountData = $data['subaccount'];

            $parentCustomerId = $subAccountData['parent_customer_id'];

            if(isset($data['all_customers_ids']) && is_array($data['all_customers_ids'])) {

                foreach($data['all_customers_ids'] AS $customerId) {
                    Mage::getModel('cminds_multiusersubaccounts/binded')
                        ->getCollection()
                        ->addFilter('subaccount_id', $subAccountId)
                        ->addFilter('customer_id', $customerId)
                        ->getFirstItem()
                        ->delete();

                    if(isset($data['customers_ids']) && is_array($data['customers_ids']) && in_array($customerId, $data['customers_ids'])) {
                        Mage::getModel('cminds_multiusersubaccounts/binded')
                            ->setData('subaccount_id', $subAccountId)
                            ->setData('customer_id', $customerId)
                            ->save();
                    }
                }
            }

            try {
                $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount');

                if ($subAccountId) {
                    $subAccount->load($subAccountId);
                }

                if (!empty($subAccountData['new_password'])){
                    $newPassword = $subAccountData['new_password'];
                    $subAccount->setPassword($newPassword);
                    $subAccount->sendPasswordReminderEmail();
                }
                unset($subAccountData['new_password']);

                $subAccount->addData($subAccountData);
                $subAccount->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The Sub Account has been saved.')
                );

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setSubAccountData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/subAccount/edit', array('id' => $subAccountId)));
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('adminhtml')->__('An error occurred while saving the Sub Account.'));
                $this->_getSession()->setSubAccountData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/subAccount/edit', array('id' => $subAccountId)));
                return;
            }
        }

        $this->getResponse()->setRedirect($this->getUrl('*/customer/edit/tab/customer_info_tabs_customer_edit_tab_subaccount', array('id' => $parentCustomerId)));
    }

    public function newPostAction()
    {
        $this->_initCustomer('parent_customer_id');
        $parentCustomer = Mage::registry('current_customer');
        $parentCustomerId = $parentCustomer->getId();

        $data = $this->getRequest()->getPost();
        if ($data) {
            $subAccountData = $data['subaccount'];

            try {
                $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount');

                $subAccountData['store_id'] = $parentCustomer->getStoreId();
                $subAccountData['website_id'] = $parentCustomer->getWebsiteId();

                $subAccount->setPassword($subAccountData['new_password']);
                $subAccount->sendPasswordReminderEmail();

                unset($subAccountData['new_password']);
                unset($subAccountData['password_confirmation']);

                $subAccount->addData($subAccountData);
                $subAccount->save();

                $subAccountId = $subAccount->getId();

                $parentCustomerId = $subAccountData['parent_customer_id'];

                if(isset($data['all_customers_ids']) && is_array($data['all_customers_ids'])) {

                    foreach($data['all_customers_ids'] AS $customerId) {
                        Mage::getModel('cminds_multiusersubaccounts/binded')
                            ->getCollection()
                            ->addFilter('subaccount_id', $subAccountId)
                            ->addFilter('customer_id', $customerId)
                            ->getFirstItem()
                            ->delete();

                        if(isset($data['customers_ids']) && is_array($data['customers_ids']) && in_array($customerId, $data['customers_ids'])) {
                            Mage::getModel('cminds_multiusersubaccounts/binded')
                                ->setData('subaccount_id', $subAccountId)
                                ->setData('customer_id', $customerId)
                                ->save();
                        }
                    }
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The Sub Account has been saved.')
                );

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setSubAccountData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/subAccount/edit', array('parent_customer_id' => $parentCustomerId)));
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('adminhtml')->__('An error occurred while saving the Sub Account.'));
                $this->_getSession()->setSubAccountData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/subAccount/edit', array('parent_customer_id' => $parentCustomerId)));
                return;
            }
        }

        $this->getResponse()->setRedirect($this->getUrl('*/customer/edit/tab/customer_info_tabs_customer_edit_tab_subaccount', array('id' => $parentCustomerId)));
    }

    public function bindedGridAction() {

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cminds_multiusersubaccounts/adminhtml_subAccount_edit_tab_binded','subaccount_edit_tab_binded')
                ->setUseAjax(true)
                ->toHtml());
    }
}