<?php

/**
 * Product:       Xtento_XtCore
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2019-05-07T22:24:17+02:00
 * File:          app/code/local/Xtento/XtCore/Helper/Utils.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_XtCore_Helper_Utils extends Mage_Core_Helper_Abstract
{
    protected $_modules = false;

    protected $_versionCorrelationEE_CE = array(
        '1.9.1.0' => '1.4.2.0',
        '1.9.1.1' => '1.4.2.0',
        '1.10.0.1' => '1.5.0.1',
        '1.10.1.0' => '1.5.1.0',
        '1.10.1.1' => '1.5.1.0',
        '1.11.0.0' => '1.6.0.0',
        '1.11.0.2' => '1.6.0.0',
        '1.11.1.0' => '1.6.1.0',
        '1.11.2.0' => '1.6.1.0',
        '1.12.0.0' => '1.7.0.0',
        '1.12.0.1' => '1.7.0.0',
        '1.12.0.2' => '1.7.0.0',
        '1.13.0.0' => '1.8.0.0',
        '1.13.0.2' => '1.8.0.0',
        '1.13.1.0' => '1.8.1.0',
        '1.14.0.0' => '1.9.0.0',
        '1.14.0.1' => '1.9.0.1',
        '1.14.1.0' => '1.9.1.0',
        '1.14.1.1' => '1.9.1.1',
        '1.14.2.0' => '1.9.2.0',
        '1.14.2.1' => '1.9.2.1',
        '1.14.2.2' => '1.9.2.2',
        '1.14.2.3' => '1.9.2.3',
        '1.14.2.4' => '1.9.2.4',
        '1.14.3.0' => '1.9.3.0',
        '1.14.3.1' => '1.9.3.1',
        '1.14.3.2' => '1.9.3.2',
        '1.14.3.3' => '1.9.3.3',
        '1.14.3.4' => '1.9.3.4',
        '1.14.3.5' => '1.9.3.5',
        '1.14.3.6' => '1.9.3.6',
        '1.14.3.7' => '1.9.3.7',
        '1.14.3.8' => '1.9.3.8',
        '1.14.3.9' => '1.9.3.9',
        '1.14.3.10' => '1.9.3.10',
        '1.14.4.0' => '1.9.4.0',
        '1.14.4.1' => '1.9.4.1'
    );

    protected $_versionCorrelationPE_CE = array(
        '1.9.1.0' => '1.4.2.0',
        '1.9.1.1' => '1.4.2.0',
        '1.10.0.1' => '1.5.0.1',
        '1.10.1.0' => '1.5.1.0',
        '1.11.0.0' => '1.6.0.0',
        '1.11.1.0' => '1.6.1.0',
    );

    /* Thanks for the inspiration to Sortal. */
    public function mageVersionCompare($version1, $version2, $operator)
    {
        // Detect edition by included modules
        if (!$this->_modules) {
            $this->_modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        }

        $version1 = preg_replace("/[^0-9\.]/", "", $version1);

        if (in_array('Enterprise_CatalogPermissions', $this->_modules)) {
            // Detected enterprise edition
            if (!isset($this->_versionCorrelationEE_CE[$version1])) {
                $version1 = str_replace(array('1.11.', '1.12.', '1.13.', '1.14.'), array('1.6.', '1.7.', '1.8.', '1.9.'), $version1);
                return version_compare($version1, $version2, $operator);
            } else {
                return version_compare($this->_versionCorrelationEE_CE[$version1], $version2, $operator);
            }
        } elseif (in_array('Enterprise_Enterprise', $this->_modules)) {
            // Detected professional edition
            if (!isset($this->_versionCorrelationPE_CE[$version1])) {
                return version_compare($version1, $version2, $operator);
            } else {
                return version_compare($this->_versionCorrelationPE_CE[$version1], $version2, $operator);
            }
        } else {
            // Detected community edition
            return version_compare($version1, $version2, $operator);
        }
    }

    // Check if a third party extension is installed and enabled
    public function isExtensionInstalled($extensionIdentifier)
    {
        if (!$this->_modules) {
            $this->_modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        }
        // Possible improvement: Check if "active" is "true" as otherwise disabled modules will return true as well
        if (in_array($extensionIdentifier, $this->_modules)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Is the module running in a Magento Professional or Enterprise Edition installation?
     */
    public function getIsPEorEE()
    {
        // Detect edition by included modules
        if (!$this->_modules) {
            $this->_modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        }

        if (in_array('Enterprise_CatalogPermissions', $this->_modules)) {
            // Detected enterprise edition
            return true;
        } elseif (in_array('Enterprise_Enterprise', $this->_modules)) {
            // Detected professional edition
            return true;
        } else {
            // Detected community edition
            return false;
        }
    }

    public function isCronRunning()
    {
        return Mage::getModel('xtcore/observer_cron')->checkCronjob();
    }

    public function getLastCronExecution()
    {
        return Mage::getModel('xtcore/observer_cron')->getLastExecution();
    }

    /**
     * @param $newMemoryLimit
     *
     * Increase memory limit to $newMemoryLimit, but only if current value is lower
     */
    public function increaseMemoryLimit($newMemoryLimit)
    {
        $currentLimit = ini_get('memory_limit');
        if ($currentLimit == -1) {
            // No limit, no need to increase
            return true;
        }
        $currentLimitInBytes = $this->_convertToByte($currentLimit);
        $newMemoryLimitInBytes = $this->_convertToByte($newMemoryLimit);
        if ($currentLimitInBytes < $newMemoryLimitInBytes) {
            @ini_set('memory_limit', $newMemoryLimit);
            return true;
        } else {
            return false;
        }
    }

    protected function _convertToByte($value)
    {
        if (stripos($value, 'G') !== false) {
            return (int)$value * pow(1024, 3);
        } elseif (stripos($value, 'M') !== false) {
            return (int)$value * 1024 * 1024;
        } elseif (stripos($value, 'K') !== false) {
            return (int)$value * 1024;
        }
        return (int)$value;
    }

    /**
     * @return null|Zend_Mail_Transport_Smtp
     *
     * Support for custom email transports
     */
    public function getEmailTransport()
    {
        $transport = null;
        if (Mage::helper('xtcore/utils')->isExtensionInstalled('Aschroder_SMTPPro') && Mage::helper('smtppro')->isEnabled()) {
            // SMTPPro extension
            $transport = Mage::helper('smtppro')->getTransport();
        } else if (Mage::helper('xtcore/utils')->isExtensionInstalled('AW_Customsmtp') && Mage::getStoreConfig('customsmtp/general/mode') != AW_Customsmtp_Model_Source_Mode::OFF) {
            // AW_Customsmtp extension
            $config = array(
                'port' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_PORT), //optional - default 25
                'auth' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_AUTH),
                'username' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_LOGIN),
                'password' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_PASSWORD)
            );

            $needSSL = Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_SSL);
            if (!empty($needSSL)) {
                $config['ssl'] = Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_SSL);
            }

            $transport = new Zend_Mail_Transport_Smtp(Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_HOST), $config);
        }
        return $transport;
    }

    /**
     * @param $moduleName
     * @param $dataModelName
     *
     * @return string
     */
    public function getExtensionStatusString($moduleName, $dataModelName)
    {
        // Set up cache, using the Magento cache doesn't make sense as it won't cache if cache is disabled
        try {
            $cacheBackend = new Zend_Cache_Backend();
            $cache = Zend_Cache::factory('Core', 'File', array('lifetime' => 43200), array('cache_dir' => $cacheBackend->getTmpDir()));
        } catch (Exception $e) {
            return '';
        }
        $cacheKey = 'extstatus_' . $moduleName;
        if ($moduleName !== '') {
            $moduleVersion = (string)@Mage::getConfig()->getNode()->modules->{$moduleName}->version;
            if (!empty($moduleVersion)) {
                $cacheKey .= '_' . str_replace('.', '_', $moduleVersion);
            }
        }
        $cacheKey .= substr(md5(__DIR__), 0, 10); // Unique per Magento installation
        // Is the response cached?
        $cachedHtml = $cache->load($cacheKey);
        #$cachedHtml = false; // Test: disable cache
        if ($cachedHtml !== false && $cachedHtml !== '') {
            $storeJson = $cachedHtml;
        } else {
            try {
                $dataModel = Mage::getSingleton($dataModelName);
                $dataModel->afterLoad();
                // Fetch info whether updates for the module are available
                $url = 'ht' . 'tp://w' . 'ww.' . 'xte' . 'nto.' . 'co' . 'm/li' . 'cense/status';
                $version = Mage::getVersion();
                $extensionVersion = $dataModel->getValue();
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $streamContext = stream_context_create(array('http' => array('timeout' => 5)));
                    $storeJson = file_get_contents($url . '?version=' . $version . '&d=' . $extensionVersion, false, $streamContext);
                } else {
                    $client = new Zend_Http_Client($url, array('timeout' => 5));
                    $client->setParameterGet('version', $version);
                    $client->setParameterGet('d', $extensionVersion);
                    $response = $client->request('GET');
                    // Post version
                    /*$client = new Zend_Http_Client($url, array('timeout' => 5));
                    $client->setParameterPost('version', $version);
                    $client->setParameterPost('d', $extensionVersion);
                    $response = $client->request('POST');*/
                    $storeJson = $response->getBody();
                }
                $cache->save($storeJson, $cacheKey);
            } catch (Exception $e) {
                $cache->save('<!-- Empty/error response -->', $cacheKey);
                return '';
            }
        }
        if (preg_match('/There has been an error processing your request/', $storeJson)) {
            return '';
        }
        $storeJson = @json_decode($storeJson, true);
        if (isset($storeJson['html'])) {
            $statusHtml = $storeJson['html'];
        } else {
            return '';
        }
        return $statusHtml;
    }
}