<?php
/**
 * MissionRS_Purchaseorder_Model_System_Config_Source_Customergroup
 *
 * @category	MissionRS
 * @package     MissionRS_Purchaseorder
 * @author 		Victor Cortez <victorc@missionrs.com>
 */

class MissionRS_Purchaseorder_Model_System_Config_Source_Customergroup
{
	public function toOptionArray()
	{
		return Mage::getResourceModel('customer/group_collection')->toOptionArray();
	}
}