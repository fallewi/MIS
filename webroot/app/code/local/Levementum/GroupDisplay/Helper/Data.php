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
	private function getGroup ()
	{
		$login = Mage::getSingleton('customer/session')->isLoggedIn();
		if ($login){
			$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId(); //Get Customers Group ID
			$group = Mage::getModel('customer/group')->load($groupId);
			return $group;
		}
		else {
			return false;
		}
	}

	public function getCategoryURL ()
	{
		$group = $this->getGroup();
		$groupName = $group->getCustomerGroupCode();
		$category = Mage::getModel('catalog/category')->loadByAttribute('name', $groupName);
		$url = "/";

		if ($category)
		{
			$url = $category->getUrl();
		}

		return $url;
	}

	public function getGroupImage ()
	{
		$group = $this->getGroup();
		$groupImage =  $group->getCustomerGroupImageUrl();
		$groupImageTag = "";

		if(!empty($groupImage))
		{
			$groupImageTag = "<img src=\"".Mage::getUrl('media').$groupImage."\" alt=\"\" style=\"max-width:150px;\" />";
		}
		return $groupImageTag;
	}

	public function getMatchedImage ()
	{
		$group = $this->getGroup();
		$result = array();

		if ( is_object($group) && $group->getId() )
		{
			$image = $group->getCustomerGroupImageUrl();

			if (!empty($image))
			{
				$result[ 'group_id' ] = $group->getId();
				$result['group_code'] = $group->getCode();
				$result['image_url'] = Mage::getUrl('media').$group->getCustomerGroupImageUrl();
				return $result;
			}
		}
		return false;
	}
}
