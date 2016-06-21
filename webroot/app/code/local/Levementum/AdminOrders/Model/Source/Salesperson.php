<?php
/**
 * @category    Levementum
 * @package     Levementum_
 * @file        Adminuser.php
 * @author      icoast@levementum.com
 * @date        10/16/13 12:36 PM
 * @brief       
 * @details     
 */

class Levementum_AdminOrders_Model_Source_Salesperson {
    protected $_options;

    protected function _getOptions()
    {
        if (!$this->_options)
        {
            $collection = Mage::getResourceModel('admin/user_collection');
            $collection->join(array('role' => 'admin/role'),'main_table.user_id=role.user_id');
            $collection->join(array('role2' => 'admin/role'),'role.parent_id=role2.role_id',array('role_group' => 'role_name', 'role_group_id' => 'role_id'));

            //role is admin or salesperson
            $collection->addFieldToFilter(
                array(
                    'role2.role_name',
                    'role2.role_name',
                    'role2.role_name'
                ),
                array(
                    array('eq'=>Mage::helper('adminorders')->getSalespersonRoleName()),
                    array('eq'=>"Super User"),
                    array('eq'=>"Administrators")
                )
            );
            $collection->addFieldToFilter('is_active',"1");
            $collection->setOrder('firstname', 'ASC');
            $collection->setOrder('lastname', 'ASC');
            $collection->setOrder('username', 'ASC');

            $this->_options = array();
            foreach ($collection as $item) {
                $this->_options[$item->getUsername()] = $item->getFirstname().' '.$item->getLastname();
            }
        }
        return $this->_options;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array();
            foreach ($this->_getOptions() as $value => $label) {
                $this->_options[] = array(
                    'value' => $value,
                    'label' => $label
                );
            }
        }

        return $this->_options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_getOptions();
    }

}