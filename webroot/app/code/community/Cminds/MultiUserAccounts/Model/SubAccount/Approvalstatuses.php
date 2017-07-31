<?php

class Cminds_MultiUserAccounts_Model_SubAccount_Approvalstatuses extends Varien_Object
{
    const ASK_NOT_SENT = 1;
    const ASK_SENT = 2;
    const ASK_APPROVED = 3;

    /**
     * Retrieve option array
     *
     * @return array
     */
    static public function getOptionArray()
    {

        return array(
            self::ASK_NOT_SENT => Mage::helper('cminds_multiuseraccounts')->__('Ask for Approval was not sent'),
            self::ASK_SENT => Mage::helper('cminds_multiuseraccounts')->__('Ask for Approval was sent'),
            self::ASK_APPROVED => Mage::helper('cminds_multiuseraccounts')->__('Cart approved'),
        );

    }

    static public function getWritePermission()
    {
        return array(
            self::PERMISSION_WRITE,
            self::PERMISSION_ORDER_WRITE,
        );
    }

    static public function getOrderCreationPermission()
    {
        return array(
            self::PERMISSION_ORDER,
            self::PERMISSION_ORDER_WRITE,
        );
    }

    static public function getNeedApprovalPermission()
    {
        return array(
            self::PERMISSION_NEED_APPROVAL,
        );
    }

    /**
     * Retrieve all options
     *
     * @return array
     */
    static public function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, array('value' => '', 'label' => ''));
        return $options;
    }

    /**
     * Retireve all options
     *
     * @return array
     */
    static public function getAllOptions()
    {
        $res = array();
        $res[] = array('value' => '', 'label' => Mage::helper('cminds_multiuseraccounts')->__('-- Please Select --'));
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
                'value' => $index,
                'label' => $value
            );
        }
        return $res;
    }

    /**
     * Retrieve option text
     *
     * @param int $optionId
     * @return string
     */
    static public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
