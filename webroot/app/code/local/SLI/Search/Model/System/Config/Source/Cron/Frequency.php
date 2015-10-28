<?php
/**
 * Copyright (c) 2013 S.L.I. Systems, Inc. (www.sli-systems.com) - All Rights Reserved
 * This file is part of Learning Search Connect.
 * Learning Search Connect is distribute under license,
 * go to www.sli-systems.com/LSC for full license details.
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 *
 * Cron Frequency Model
 * 
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Model_System_Config_Source_Cron_Frequency {

    protected static $_options;

    const CRON_3_HOURLY    	= '3H';
    const CRON_6_HOURLY    	= '6H';
    const CRON_12_HOURLY        = '12H';
    const CRON_DAILY    	= 'D';
    const CRON_WEEKLY   	= 'W';
    const CRON_MONTHLY  	= 'M';

    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = array(
                array(
                    'label' => Mage::helper('cron')->__('3 Hourly'),
                    'value' => self::CRON_3_HOURLY,
                ),
                array(
                    'label' => Mage::helper('cron')->__('6 Hourly'),
                    'value' => self::CRON_6_HOURLY,
                ),
                array(
                    'label' => Mage::helper('cron')->__('12 Hourly'),
                    'value' => self::CRON_12_HOURLY,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Daily'),
                    'value' => self::CRON_DAILY,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Weekly'),
                    'value' => self::CRON_WEEKLY,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Monthly'),
                    'value' => self::CRON_MONTHLY,
                ),

            );
        }
        return self::$_options;
    }
}

