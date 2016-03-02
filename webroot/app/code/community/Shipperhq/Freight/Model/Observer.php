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

class Shipperhq_Freight_Model_Observer extends Mage_Core_Model_Abstract
{
    /*
     * Refresh carriers in configuration pane
     *
     */
    public function hookToControllerActionPreDispatch($observer)
    {
        $actionName = $observer->getEvent()->getControllerAction()->getFullActionName();

        $actionNames = array('checkout_cart_estimatePost','checkout_cart_index',
            'checkout_cart_updatePost','checkout_cart_delete');

        //we compare action name to see if that's action for which we want to add our own event
        if (in_array($actionName, $actionNames)) {
            switch($actionName) {
                case 'checkout_cart_updatePost':
                case 'checkout_cart_delete':
                case 'checkout_cart_index':
                 //   $this->getFreightAccessorials($observer);
                    break;
                case 'checkout_cart_estimatePost':
                    $this->processFreightAccessorials($observer);
                    break;
            }
        }

    }

    /*
     * Set selected options
     */
    public function saveOptionsSelected($observer)
    {
        $shippingmethod = $observer->getEvent()->getRequest()->getParam('shipping_method');
        if($shippingmethod == '' || is_null($shippingmethod)
            || !Mage::helper('shipperhq_shipper')->isModuleEnabled('Shipperhq_Freight')) {
            return;
        }

        $shippingAddress = $observer->getEvent()->getQuote()->getShippingAddress();
        $params = $observer->getEvent()->getRequest()->getParams();

        $options = Mage::helper('shipperhq_freight')->getAllPossibleOptions();
        $carrierCodeSplit = explode('_', $shippingmethod);
        $carrierCode = $carrierCodeSplit[0];
        foreach($options as $optionCode) {
            if (array_key_exists($optionCode  .'_' .$carrierCode, $params)) {
                $shippingAddress->setData($optionCode, $params[$optionCode  .'_' .$carrierCode]);
            }
        }
        $shippingAddress->save();

    }

    public function saveOptionsSelectedInAdmin($observer)
    {
        $requestData = $observer->getRequest();
        $orderData = array();

        if (isset($requestData['order'])) {
            $orderData = $requestData['order'];
            if(isset($requestData['shipping_method_flag'])) {
                $orderData = $requestData;
            }
        }
        if ($orderData
            && !empty($orderData['shipping_method_flag'])
            && !empty($orderData['shipping_method'])) {
            $shippingmethod = $orderData['shipping_method'];
            $quote = $observer->getOrderCreateModel();
            $shippingAddress = $quote->getShippingAddress();

            $options = Mage::helper('shipperhq_freight')->getAllPossibleOptions();
            $carrierCodeSplit = explode('_', $shippingmethod);
            $carrierCode = $carrierCodeSplit[0];
            foreach($options as $optionCode) {
                if (array_key_exists($optionCode  .'_' .$carrierCode, $orderData)) {
                    $shippingAddress->setData($optionCode, $orderData[$optionCode  .'_' .$carrierCode]);
                }
            }
            $shippingAddress->save();
        }
    }

    protected function getFreightAccessorials($observer)
    {
        $quote = $this->getQuote();
        $response = Mage::getSingleton('shipperhq_freight/service_accessorials')->retrieveAccessorials($quote);


    }

    protected function processFreightAccessorials($observer)
    {
        $request = $observer->getControllerAction()->getRequest();
        $country = (string)$request->getParam('country_id');
        $postcode = (string)$request->getParam('estimate_postcode');
        $city = (string)$request->getParam('estimate_city');
        $regionId = (string)$request->getParam('region_id');
        $region = (string)$request->getParam('region');
        $params = $request->getParams();
        $shipAddress =   $this->getQuote()->getShippingAddress();

        $shipAddress->setCountryId($country)
            ->setCity($city)
            ->setPostcode($postcode)
            ->setRegionId($regionId)
            ->setRegion($region)
            ->setCollectShippingRates(true);
        if(isset($params['liftgate_required'])) {
            $shipAddress->setLiftgateRequired((string)$params['liftgate_required']);
        }
        if(isset($params['notify_required'])) {
            $shipAddress->setNotifyRequired((string)$params['notify_required']);
        }
        if(isset($params['inside_delivery'])) {
            $shipAddress->setInsideDelivery((string)$params['inside_delivery']);
        }
        if(isset($params['destination_type'])) {
            $shipAddress->setDestinationType((string)$params['destination_type']);
        }
        $this->getQuote()->getShippingAddress()->save();
    }

    protected function getQuote()
    {
        return $this->getCart()->getQuote();
    }

    protected function getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

  /*  protected function _getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    protected function _getAdminQuote()
    {
        return $this->_getAdminSession()->getQuote();
    }*/

}