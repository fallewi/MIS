<?php

class Cminds_MultiUserAccounts_Model_Enterprise_Pci_Observer extends Enterprise_Pci_Model_Observer
{
    public function upgradeCustomerPassword($observer)
    {
        $helper = Mage::helper('cminds_multiuseraccounts');

        if ($helper->isSubAccountMode()) {
            return;
        }

        $password = $observer->getEvent()->getPassword();
        $model = $observer->getEvent()->getModel();

        $encryptor = $this->_getCoreHelper()->getEncryptor();
        $isPasswordUpdateRequired = !$encryptor->validateHashByVersion($password,
            $model->getPasswordHash());

        if ($isPasswordUpdateRequired) {
            $model->changePassword($password, false);
        }
    }
}