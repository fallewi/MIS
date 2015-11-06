<?php
/**
 * @category    Levementum
 * @package     Levementum_
 * @file        Adminroles.php
 * @author      icoast@levementum.com
 * @date        10/16/13 12:47 PM
 * @brief       
 * @details     
 */

class Levementum_AdminOrders_Model_System_Config_Source_Adminroles
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $collection = Mage::getResourceModel('admin/role_collection')->setRolesFilter();
            $this->_options = array();
            foreach ($collection as $item) {
                $this->_options[] = array(
                    'label' => $item->getRoleName(),
                    'value' => $item->getId()
                );
            }
        }
        return $this->_options;
    }
}