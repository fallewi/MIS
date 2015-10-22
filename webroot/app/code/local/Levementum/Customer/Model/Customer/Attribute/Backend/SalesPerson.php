<?php
/**
* @namespace    Levementum
* @module       ${MODULE}
* @file         SalesPerson.php
* @author       dbeljic@levementum.com
* @date         4/24/14 1:49 PM
* @brief
* @details
*/

class Levementum_Customer_Model_Customer_Attribute_Backend_Salesperson extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Validate object
     *
     * @param Varien_Object $object
     * @throws Mage_Eav_Exception
     * @return boolean
     */
    public function validate($object)
    {
       return parent::validate($object);
    }
}

