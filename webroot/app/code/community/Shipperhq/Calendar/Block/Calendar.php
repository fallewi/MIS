<?php
/**
 *
 * Webshopapps Shipping Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * Shipper HQ Shipping
 *
 * @category ShipperHQ
 * @package ShipperHQ_Shipping_Carrier
 * @copyright Copyright (c) 2014 Zowta LLC (http://www.ShipperHQ.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author ShipperHQ Team sales@shipperhq.com
 */

/**
 * Shipper shipping model
 *
 * @category ShipperHQ
 * @package ShipperHQ_Shipper
 */
class Shipperhq_Calendar_Block_Calendar extends Shipperhq_Frontend_Block_Checkout_AbstractBlock
{
    protected $_calendarDetails;

    public function setCalendarDetails($details)
    {
        $this->_calendarDetails = $details;
    }

    public function oneStepCheckoutEnabled()
    {
        return Mage::helper('shipperhq_shipper')->isModuleEnabled('Idev_OneStepCheckout', 'onestepcheckout/general/rewrite_checkout_links');
    }

    public function getDelSlotHtmlSelect($carrierCodeInsert, $carriergroupInsert)
    {
        $className = 'calendar-slot-select';
//        if(Mage::getStoreConfig('carriers/shipper/new_styling')) {
//            $className .= ' shq-input';
//        }

        $id = 'del_slot_select'.$carrierCodeInsert .$carriergroupInsert;
        $selectedTimeSlot =  $this->getAddress()->getTimeSlot();
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('del_slot'.$carrierCodeInsert.$carriergroupInsert)
            ->setId($id)
            ->setValue($selectedTimeSlot)
            ->setClass($className);

        return $select->getHtml();
    }

    public function getCalendarDetails($carriergroupId, $code)
    {
        $calendarDetails = Mage::helper('shipperhq_shipper')->getQuoteStorage()->getCalendarDetails();
        return $calendarDetails;
    }

    public function getCarriergroupInsert($carriergroupId) {
        if (is_empty($carriergroupId)) {
            return '';
        } else {
            return '_'.$carriergroupId;
        }
    }
}