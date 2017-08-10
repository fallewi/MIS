<?php

class Cminds_MultiUserAccounts_Model_SubAccount_Limits extends Varien_Object
{
    const LIMIT_NONE = 0;
    const LIMIT_DAY = 1;
    const LIMIT_MONTH = 2;
    const LIMIT_YEAR = 3;
    const LIMIT_ORDER = 4;

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $helper = Mage::helper('cminds_multiuseraccounts');

        return array(
            self::LIMIT_NONE => $helper->__('Not limited'),
            self::LIMIT_DAY => $helper->__('Day'),
            self::LIMIT_MONTH => $helper->__('Month'),
            self::LIMIT_YEAR => $helper->__('Year'),
            self::LIMIT_ORDER => $helper->__('Order')
        );
    }

    /**
     * Retrieve all options
     *
     * @return array
     */
    public function getAllOption()
    {
        $options = self::getOptionArray();

        return $options;
    }

    /**
     * Retrieve all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $res = array();
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
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    public function getOptionValues()
    {
        return array(
            self::LIMIT_NONE,
            self::LIMIT_DAY,
            self::LIMIT_MONTH,
            self::LIMIT_YEAR,
            self::LIMIT_ORDER,
        );
    }
}
