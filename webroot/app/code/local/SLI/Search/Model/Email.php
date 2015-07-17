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
 * Feed generation email
 * 
 * @package SLI
 * @subpackage Search
 */

class SLI_Search_Model_Email extends Mage_Core_Model_Abstract {

    /**
     * Set up some default variables that can be set from sys config
     */
    public function __construct() {
        $this->setFromName(Mage::getStoreConfig('trans_email/ident_general/name'));
        $this->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
        $this->setType('text');
    }

    /**
     * SLI feed generation email subject
     *
     * @return string
     */
    public function getSubject() {
        return $this->getData('subject');
    }

    public function getEmail(){
        return $this->getData('email');
    }
    /**
     * SLI feed generation email body
     *
     * @return string
     */
    public function getBody() {
        return <<<BODY
Status:
{$this->getData('msg')}

Please check the sli log files for further information.

BODY;
    }

    public function send() {
        $email = $this->getEmail();
        if ($email) {
            mail($email, $this->getSubject(), $this->getBody(), "From: {$this->getFromName()} <{$this->getFromEmail()}>\r\nReply-To: {$this->getFromEmail()}");
        }
    }

}