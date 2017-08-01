<?php

class Cminds_MultiUserAccounts_Model_SubAccount_Emails extends Varien_Object
{
    const EMAIL_MASTER = 1;
    const EMAIL_SUBACCOUNT = 2;
    const EMAIL_SUBACCOUNT_MASTER = 3;

    /**
     * Retrieve option array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::EMAIL_MASTER => Mage::helper('cminds_multiuseraccounts')->__('Master'),
            self::EMAIL_SUBACCOUNT => Mage::helper('cminds_multiuseraccounts')->__('Sub Account'),
            self::EMAIL_SUBACCOUNT_MASTER => Mage::helper('cminds_multiuseraccounts')->__('Sub Account & Master'),
        );
    }

    public function toOptionArray()
    {

        $canSee = array();
        foreach ($this->getOptionArray() as $value => $label) {
            $canSee[] = array(
                'value' => $value,
                'label' => $label,
            );
        }
        return $canSee;
    }
}