<?php

class BlueAcorn_Southware_Model_Order_Ebridge
{
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
                                  <status>NEW</status>
                                  <docType>ORDERS</docType>
                                  <partner>Mission Restaurant Supply</partner>
                                  <fromDate>2016-05-12</fromDate>
                                  <toDate>'.date('Y-m-d',time()+86400).'</toDate>
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
            ); //SOAPAction: your op URL

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
            $xmlRespone = html_entity_decode($response, ENT_XML1);

            $p = xml_parser_create();
            xml_parse_into_struct($p, $xmlRespone, $vals, $index);
            xml_parser_free($p);

            foreach ($index['_X0023_TEMP_LIST'] as $key => $value)
            {
                if(isset($vals[$value]['attributes']))
                {
                    $this->getDocument($vals[$value]['attributes']);
                }
            }

        }
        catch (Exception $e) {
            Mage::logException($e);
        }
    }

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

            foreach ($orderDataArray['OrderHeader']['ListOfNameValueSet']['NameValueSet']['ListOfNameValuePair']['NameValuePair'] as $item)
            {
                if($item['Name'] === "MagentoIncrementId")
                    $this->setSouthwareOrderId($southwareOrderId, $item['value']);
            }
        }
        catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function setSouthwareOrderId($southwareOrderId, $incrementId)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
        $orderId = $order->getId();

        Mage::getResourceModel('sales/order')->setSouthWareOrderId($orderId, $southwareOrderId);
    }
}