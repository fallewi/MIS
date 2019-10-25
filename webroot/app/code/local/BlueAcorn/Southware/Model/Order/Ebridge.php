<?php

class BlueAcorn_Southware_Model_Order_Ebridge
{
    /**
     * Gets list of all new documents from eBridge
     */
    public function getDocumentList()
    {
        $soapUrl = Mage::getStoreConfig('blueacorn_southware/api_settings/api_url');
        $soapUser = Mage::getStoreConfig('blueacorn_southware/api_settings/api_user');
        $soapPassword = Mage::helper('core')->decrypt(Mage::getStoreConfig('blueacorn_southware/api_settings/api_password'));

        try {
            // xml post structure
            $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
                            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                              <soap:Body>
                                <GetDocumentList xmlns="eBridge.WebServices">
                                  <login>'.$soapUser.'</login>
                                  <password>'.$soapPassword.'</password>
                                  <status>All</status>
                                  <docType>Shipment</docType>
                                  <partner>Mission Restaurant Supply</partner>
                                  <fromDate>'.Mage::getModel('core/date')->date('Y-m-d').'</fromDate>
                                  <toDate>'.Mage::getModel('core/date')->date('Y-m-d',time()+86400).'</toDate>
                                </GetDocumentList>
                              </soap:Body>
                            </soap:Envelope>';   // data from the form, e.g. some ID number
            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: eBridge.WebServices/GetDocumentList",
                "Content-length: ".strlen($xml_post_string),
            ); //SOAPAction

            $url = $soapUrl . '?GetDocumentList';

            // PHP cURL  for https connection with auth
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            curl_close($ch);

            $debugData['result'] = $response;
            // decode xml entities
            $xmlRespone = html_entity_decode($response, ENT_XML1);

            $p = xml_parser_create();
            // turn xml into an array
            xml_parse_into_struct($p, $xmlRespone, $vals, $index);
            xml_parser_free($p);

            if( isset($index['_X0023_TEMP_LIST'])){
                foreach ($index['_X0023_TEMP_LIST'] as $key => $value)
                {
                    if(isset($vals[$value]['attributes']))
                    {
                        $this->getDocument($vals[$value]['attributes']);
                    }
                }
            }
        }
        catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Gets documents from Document List
     */
    public function getDocument($data)
    {
        $soapUrl = Mage::getStoreConfig('blueacorn_southware/api_settings/api_url');
        $soapUser = Mage::getStoreConfig('blueacorn_southware/api_settings/api_user');
        $soapPassword = Mage::helper('core')->decrypt(Mage::getStoreConfig('blueacorn_southware/api_settings/api_password'));

        try {
            $url = $soapUrl."/GetDocument?login=".$soapUser."&password=".$soapPassword."&sys_no=".$data['DOC_SYS_NO'];
            $xml = simplexml_load_file($url);
            $xml = html_entity_decode($xml, ENT_XML1);
            $xmlString = simplexml_load_string(str_replace('core:', '', $xml));
            $jsonOrderData = json_encode($xmlString);
            $orderDataArray = json_decode($jsonOrderData, true);
            $southwareOrderId = $orderDataArray['OrderHeader']['OrderNumber']['SellerOrderNumber'];
            $southwareCustomerId = $orderDataArray['OrderHeader']['OrderParty']['BuyerParty']['ListOfIdentifier']['Identifier']['Ident'];

            foreach ($orderDataArray['OrderHeader']['ListOfNameValueSet']['NameValueSet']['ListOfNameValuePair']['NameValuePair'] as $item)
            {
                if($item['Name'] === "MagentoIncrementId") {
                    $this->setSouthwareId($southwareOrderId, $item['Value'], $southwareCustomerId);
                }
            }
        }
        catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Sets the southware order ID in Magento
     *
     * @param $southwareOrderId
     * @param $incrementId
     * @param $southwareCustomerId
     */
    public function setSouthwareId($southwareOrderId, $incrementId, $southwareCustomerId)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
        $orderId = $order->getId();
        $customerEmail = $order->getCustomerEmail();

        if(!$order->getCustomerIsGuest()){
            $this->setSouthwareCustomerId($customerEmail, $southwareCustomerId);
        }

        Mage::getResourceModel('sales/order')->setSouthWareOrderId($orderId, $southwareOrderId);
    }

    /**
     * Function that sets the Southware Customer ID of a customer based on the email supplied by Southware
     *
     * @param $customerEmail
     * @param $southwareCustomerId
     */
    public function setSouthwareCustomerId($customerEmail, $southwareCustomerId)
    {
        $defaultStoreId = Mage::app()->getDefaultStoreView()->getId();
        $customer = Mage::getModel("customer/customer");

        $customer->setWebsiteId($defaultStoreId);
        $customer->loadByEmail($customerEmail);
        $customer->setSouthwareCustomerId($southwareCustomerId);

        $customer->save();
    }
}