<?php

/**
 * @category    Levementum
 * @package     ${MODULE}
 * @file        ${FILE_NAME}
 * @author      jjuleff@levementum.com
 * @date        4/4/14
 * @brief
 * @details
 */
class Levementum_GroupDisplay_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getMatchedImage ()
	{
		$result = array();

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
		$group = Mage::getModel('customer/group')->load($groupId);
		if ( is_object($group) && $group->getId () )
		{
			$image = $group->getCustomerGroupImageUrl();

			if (!empty($image))
			{
				$result[ 'group_id' ] = $group->getId ();
				$result['group_code'] = $group->getCode();
				$result['image_url'] = Mage::getUrl('media').$group->getCustomerGroupImageUrl();
				return $result;
			}
		}
		return false;
	}
}