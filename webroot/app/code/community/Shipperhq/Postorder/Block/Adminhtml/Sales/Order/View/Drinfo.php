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


class Shipperhq_Postorder_Block_Adminhtml_Sales_Order_View_Drinfo extends Mage_Adminhtml_Block_Sales_Order_View_Info//Mage_Adminhtml_Block_Sales_Order_Abstract
{
    public function getCarriergroupInfoHtml()
    {
        $displayValues = array('destination_type', 'customer_carrier', 'customer_carrier_ph', 'customer_carrier_account');
        $order = $this->getOrder();
        $htmlOutput='';
        $cginfo = Mage::helper('shipperhq_shipper')->decodeShippingDetails($order->getCarriergroupShippingDetails());
        $deliveryComments = $order->getShqDeliveryComments();
        if (!empty($cginfo)) {
            $carriergroupText='';
            if($order->getConfirmationNumber() != '') {
                $carriergroupText .=  'Order confirmation number: ' .$order->getConfirmationNumber() .'<br/>';
            }
            if(Mage::helper('shipperhq_shipper')->isModuleEnabled('Shipperhq_Freight')) {
                $options = Mage::helper('shipperhq_freight')->getAllNamedOptions();
            }
            else {
                $options = array();
            }
            foreach ($cginfo as $cgrp) {
                if(is_array($cgrp)) {
                    $pickupDate = $order->getPickupDate();
                    $timeSlot = $order->getTimeslot();
                    $pickupLocation = $order->getPickupLocation();
                    $dispatchDate = $order->getDispatchDate();
                    $deliveryDate = $order->getDeliveryDate();

                    $carriergroupText .= $cgrp['name'];
                    $carriergroupText .=  '<strong>: '.$cgrp['carrierTitle'];
                    $carriergroupText .= ' - '.$cgrp['methodTitle'];
                    $carriergroupText .= ' ' .$order->formatPrice($cgrp['price']).' </strong>';
                    if((array_key_exists('carrierName', $cgrp) && $cgrp['carrierName'] != '')) {
                        $carriergroupText .= '</br> Carrier: ';
                        $carriergroupText .= '' .strtoupper($cgrp['carrierName']);
                    }

                    if((array_key_exists('pickup_date', $cgrp) && $cgrp['pickup_date'] != '')) {
                        $pickupDate = $cgrp['pickup_date'];
                    }
                    if($pickupDate) {
                        $carriergroupText .= '</br> Pickup : ';
                        if(array_key_exists('location_name', $cgrp)) {
                            $pickupLocation = $cgrp['location_name'];
                        }
                        if($pickupLocation) {
                            $carriergroupText .= '' .$pickupLocation;
                        }
                        $carriergroupText .= ' ' . $pickupDate;
                        if(array_key_exists('pickup_slot', $cgrp)) {
                            $timeSlot = $cgrp['pickup_slot'];
                            $timeSlot = str_replace('_', ' - ', $cgrp['pickup_slot']);
                        }
                        if($timeSlot) {
                            $carriergroupText .= ' ' .$timeSlot .' ';
                        }
                    }
                    if(array_key_exists('dispatch_date', $cgrp)) {
                        $dispatchDate = $cgrp['dispatch_date'];
                    }
                    if($dispatchDate) {
                        $carriergroupText .='</br>' . Mage::helper('shipperhq_shipper')->__('Dispatch Date') .' : ' .$dispatchDate;
                    }

                    if(array_key_exists('delivery_date', $cgrp)) {
                        $deliveryDate = $cgrp['delivery_date'];
                    }
                    if($deliveryDate) {
                        $carriergroupText .='</br>' . Mage::helper('shipperhq_shipper')->__('Delivery Date') .' : ' .$deliveryDate;
                        if(array_key_exists('del_slot', $cgrp)) {
                            $timeSlot = $cgrp['del_slot'];
                            $timeSlot = str_replace('_', ' - ', $cgrp['del_slot']);
                        }
                        if($timeSlot) {
                            $carriergroupText .= ' ' .$timeSlot .' ';
                        }
                    }
                    foreach ($options as $code => $name) {
                        $value = false;
                        if (array_key_exists($code, $cgrp) && $cgrp[$code]!='') {
                            $value = $cgrp[$code];
                        }
                        elseif($order->getData($code)) {
                            $value = $order->getData($code);
                        }
                        if($value) {
                            $carriergroupText .= '</br>'. $name;
                            if(in_array($code, $displayValues)) {
                                $carriergroupText .=': '. $value;
                            }
                        }
                    }
                    if (array_key_exists('freightQuoteId',$cgrp) && $cgrp['freightQuoteId']!='') {
                        $carriergroupText .= ' Quote Id: '.$cgrp['freightQuoteId'];
                    }
                    $carriergroupText .= '<br/><br/>';
                }
            }
            $htmlOutput = '<div class="box-right"><div class="entry-edit">';
            $htmlOutput.= '<div class="entry-edit-head">';
            $htmlOutput.= '<h4 class="icon-head head-shipping-method">';
            if($desc = Mage::getStoreConfig(Shipperhq_Shipper_Helper_Data::SHIPPERHQ_SHIPPER_CARRIERGROUP_DESC_PATH)) {
                $heading = $desc;
            } else {
                $heading = $cgrp['mergedDescription'];
            }
            $heading = $heading .' ' .Mage::helper("shipperhq_postorder")->__("Shipping Information");
            $htmlOutput.= $heading;
            $htmlOutput.= '</h4>';
            $htmlOutput.= '</div><fieldset>';
            $htmlOutput.= $carriergroupText;
            if(!empty($deliveryComments)){
                $htmlOutput.= Mage::helper('shipperhq_shipper')->__('Delivery Comments') .' : ' . $order->getShqDeliveryComments();
            }
            $htmlOutput.= '</fieldset></div></div>';

        } else if (!empty($deliveryComments)) {
            $htmlOutput = '<div class="box-right"><div class="clear"></div><div class="entry-edit">';
            $htmlOutput.= '<div class="entry-edit-head">';
            $htmlOutput.= '<h4 class="icon-head head-shipping-method">';
            $heading = Mage::helper("shipperhq_postorder")->__("Shipping Information");
            $htmlOutput.= $heading;
            $htmlOutput.= '</h4>';
            $htmlOutput.= '</div><fieldset>';
            $htmlOutput .= Mage::helper('shipperhq_shipper')->__('Delivery Comments') .' : ' . $order->getShqDeliveryComments();
            $htmlOutput.= '</fieldset> <div class="clear"/></div></div>';
        }
        return $htmlOutput;
    }

}