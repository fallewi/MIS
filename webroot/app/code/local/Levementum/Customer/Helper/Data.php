<?php
/**
* @namespace    Levementum
* @module       ${MODULE}
* @file         Data.php
* @author       dbeljic@levementum.com
* @date         4/24/14 2:38 PM
* @brief
* @details
*/

class Levementum_Customer_Helper_Data extends Mage_Core_Helper_Abstract
{

public function getSalesPersons() {

    $collection = Mage::getResourceModel('admin/user_collection');
    $collection->join(array('role' => 'admin/role'),'main_table.user_id=role.user_id');
    $collection->join(array('role2' => 'admin/role'),'role.parent_id=role2.role_id',array('role_group' => 'role_name', 'role_group_id' => 'role_id'));
    //$collection->addFieldToFilter('role2.role_id',Mage::helper('adminorders')->getSalespersonRoleId());

    //role is admin or salesperson
    $collection->addFieldToFilter(
        array(
            'role2.role_id',
            'role2.role_id',
        ),
        array(
            array('eq'=>Mage::helper('adminorders')->getSalespersonRoleId()),
            array('eq'=>1),
        )
    );
    $sps = array();

    foreach ($collection as $item) {
        $sps[$item->getId()] = $item->getFirstname().' '.$item->getLastname();
    }

    return $sps;
}

}