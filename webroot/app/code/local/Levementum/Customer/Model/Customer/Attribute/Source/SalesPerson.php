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

class Levementum_Customer_Model_Customer_Attribute_Source_Salesperson extends Mage_Eav_Model_Entity_Attribute_Source_Boolean
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $salesPersons = Mage::helper('levementum_customer')->getSalesPersons();
            $this->_options = array();

            foreach ($salesPersons as $value => $label) {
                $this->_options[] = array(
                    'label' => $label,
                    'value' => $value
                );
            }

        }
        return $this->_options;
    }
}


