<?php

/**
 * Product:       Xtento_StockImport (2.3.6)
 * ID:            Local Deploy
 * Packaged:      2016-10-18T22:31:59+02:00
 * Last Modified: 2016-04-14T15:04:12+02:00
 * File:          app/code/local/Xtento/StockImport/Model/Source/Httpdownload.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_StockImport_Model_Source_Httpdownload extends Xtento_StockImport_Model_Source_Abstract
{
    public function testConnection()
    {
        $testResult = $this->initConnection();
        return $testResult;
    }

    public function initConnection()
    {
        $testResult = new Varien_Object();
        $testResult->setSuccess(true)->setMessage(Mage::helper('xtento_stockimport')->__('HTTP Download class initialized.'));
        $this->getSource()->setLastResult($testResult->getSuccess())->setLastResultMessage($testResult->getMessage())->save();
        return $testResult;
    }

    public function loadFiles()
    {
        // Init connection
        $this->initConnection();

        $url = $this->getSource()->getCustomFunction();
        $useBasicAuth = false;
        $username = '';
        $password = '';
        // Parse URL for username + password
        $parsedUrl = parse_url($url);
        if ($parsedUrl !== false) {
            if ($parsedUrl['scheme'] == 'http' || $parsedUrl['scheme'] == 'https') {
                if (array_key_exists('user', $parsedUrl) && $parsedUrl['user'] !== '') {
                    $url = str_replace($parsedUrl['user'] . ':', '', $url); // Update URL
                    $username = urldecode($parsedUrl['user']);
                    $useBasicAuth = true;
                }
                if (array_key_exists('pass', $parsedUrl) && $parsedUrl['pass'] !== '') {
                    $url = str_replace($parsedUrl['pass'] . '@', '', $url); // Update URL
                    $password = urldecode($parsedUrl['pass']);
                    $useBasicAuth = true;
                }
            }
        }

        $curlClient = curl_init();
        curl_setopt($curlClient, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlClient, CURLOPT_HEADER, false);
        curl_setopt($curlClient, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlClient, CURLOPT_URL, $url);
        if ($useBasicAuth) {
            curl_setopt($curlClient, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($curlClient, CURLOPT_USERPWD, "$username:$password");
        }
        curl_setopt($curlClient, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curlClient);

        if ($result === false) {
            // curl_error
            $logEntry = Mage::registry('stock_import_log');
            $logEntry->setResult(Xtento_StockImport_Model_Log::RESULT_WARNING);
            $logEntry->addResultMessage(Mage::helper('xtento_stockimport')->__('Source "%s" (ID: %s): There was a problem downloading the file "%s", probably a firewall is blocking the connection: curl_error: %s', $this->getSource()->getName(), $this->getSource()->getId(), $this->getSource()->getCustomFunction(), curl_error($curlClient)));
        }

        curl_close($curlClient);

        $filesToProcess[] = array('source_id' => $this->getSource()->getId(), 'path' => '', 'filename' => basename($this->getSource()->getCustomFunction()), 'data' => $result);

        // Return files to process
        return $filesToProcess;
    }

    public function archiveFiles($filesToProcess, $forceDelete = false)
    {

    }
}