<?php

class Cminds_MultiUserAccounts_Model_SubAccount_Permission extends Varien_Object
{
    const PERMISSION_READ = 1;
    const PERMISSION_WRITE = 2;
    const PERMISSION_ORDER = 3;
    const PERMISSION_ORDER_WRITE = 4;
    const PERMISSION_NEED_APPROVAL = 5;

    /**
     * Retrieve option array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        if (Mage::helper('cminds_multiuseraccounts')->ifShareSession()) {
            return array(
                self::PERMISSION_READ => Mage::helper('cminds_multiuseraccounts')->__('Read Only'),
                self::PERMISSION_WRITE => Mage::helper('cminds_multiuseraccounts')->__('Modify Account'),
                self::PERMISSION_ORDER => Mage::helper('cminds_multiuseraccounts')->__('Order Creation'),
                self::PERMISSION_ORDER_WRITE => Mage::helper('cminds_multiuseraccounts')->__('Order Creation & Modify Account'),
                self::PERMISSION_NEED_APPROVAL => Mage::helper('cminds_multiuseraccounts')->__('Read Only/Approval needed'),
            );
        } else {
            return array(
                self::PERMISSION_READ => Mage::helper('cminds_multiuseraccounts')->__('Read Only'),
                self::PERMISSION_WRITE => Mage::helper('cminds_multiuseraccounts')->__('Modify Account'),
                self::PERMISSION_ORDER => Mage::helper('cminds_multiuseraccounts')->__('Order Creation'),
                self::PERMISSION_ORDER_WRITE => Mage::helper('cminds_multiuseraccounts')->__('Order Creation & Modify Account'),
                self::PERMISSION_NEED_APPROVAL => Mage::helper('cminds_multiuseraccounts')->__('Order Approval needed'),
            );
        }
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

    static public function getOrderWritePermission()
    {
        return self::PERMISSION_ORDER_WRITE;
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
