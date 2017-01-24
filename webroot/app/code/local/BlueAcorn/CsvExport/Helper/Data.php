<?php

/**
 * @package     BlueAcorn\CsvExport
 * @version     1.0.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2017 Blue Acorn, Inc.
 */ 
class BlueAcorn_CsvExport_Helper_Data extends Mage_Core_Helper_Abstract
{
    const IS_ENABLED      = 'blueacorn_csvexport/general/active';
    const FILE_NAME       = 'blueacorn_csvexport/general/file_name';
    const FILE_LOCATION   = 'blueacorn_csvexport/general/file_location';
    const EXCLUDED_GROUPS = 'blueacorn_csvexport/general/excluded_groups';

    const EMAIL_ENABLED   = 'blueacorn_csvexport/email/email_active';
    const EMAIL_FROM      = 'blueacorn_csvexport/email/email_from';
    const EMAIL_SUBJECT   = 'blueacorn_csvexport/email/email_subject';
    const EMAIL_BODY      = 'blueacorn_csvexport/email/email_body';
    const EMAIL_ADDRESSES = 'blueacorn_csvexport/email/email_addresses';

    const FROM_DATE       = 'blueacorn_csvexport/manual_run/from_datetime';
    const TO_DATE         = 'blueacorn_csvexport/manual_run/to_datetime';
    const MAN_EMAIL_ADDR  = 'blueacorn_csvexport/manual_run/manual_email_address';
    const MAN_EMAIL_NAME  = 'blueacorn_csvexport/manual_run/manual_email_name';
    const SEND_ONE_EMAIL  = 'blueacorn_csvexport/manual_run/send_emails';

    /**Used to send
     * @param $fileName
     * @param null $manualFlag
     */
    public function sendMail($fileName, $manualFlag = null)
    {
        $recipients = array();
        $mail = new Zend_Mail('utf-8');
        $mailFrom = $this->getEmailFrom();
        $mailBody = $this->getEmailBody();
        $mailSubject = $this->getEmailSubject();

        if($manualFlag && $this->sendManEmail()){
            $recipients[$this->getManualEmailName()] = $this->getManualEmailAddress();
        }
        else{
            foreach($this->getEmailAddresses() as $email)
            {
                $recipients[$email['email_name']] = $email['email_address'];
            }
        }

        $mail->setBodyHtml($mailBody)
            ->setSubject($mailSubject)
            ->addTo($recipients)
            ->setFrom($mailFrom);

        try {
            $attachment = file_get_contents($fileName);
            $mail->createAttachment(
                $attachment,
                Zend_Mime::TYPE_OCTETSTREAM,
                Zend_Mime::DISPOSITION_ATTACHMENT,
                Zend_Mime::ENCODING_BASE64,
                $fileName
            );

            $mail->send();
            if($manualFlag){
                Mage::getSingleton('core/session')->addSuccess('The Marketing CSV was sent to ' . $this->getManualEmailAddress());
            }
        } catch (Exception $e) {
            Mage::log('There was issue emailing the Marketing CSV file', null, 'MarketingEmail.log');
            Mage::logException($e);
            Mage::getSingleton('core/session')->addError('There was an issue emailing the Marketing CSV file');
        }
    }

    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::IS_ENABLED);
    }

    public function getFileName()
    {
        return Mage::getStoreConfig(self::FILE_NAME);
    }

    public function getFileLocation()
    {
        return Mage::getStoreConfig(self::FILE_LOCATION);
    }

    public function getExcludedGroups()
    {
        return Mage::getStoreConfig(self::EXCLUDED_GROUPS);
    }

    public function isEmailEnabled()
    {
        return Mage::getStoreConfigFlag(self::EMAIL_ENABLED);
    }

    private function getEmailSubject()
    {
        return Mage::getStoreConfig(self::EMAIL_SUBJECT);
    }

    private function getEmailFrom()
    {
        return Mage::getStoreConfig(self::EMAIL_FROM);
    }

    private function getEmailBody()
    {
        return Mage::getStoreConfig(self::EMAIL_BODY);
    }

    private function getEmailAddresses()
    {
        return unserialize(Mage::getStoreConfig(self::EMAIL_ADDRESSES));
    }

    public function getFromDate()
    {
        return Mage::getStoreConfig(self::FROM_DATE);
    }

    public function getToDate()
    {
        return Mage::getStoreConfig(self::TO_DATE);
    }

    public function getManualEmailName()
    {
        return Mage::getStoreConfig(self::MAN_EMAIL_NAME);
    }

    public function getManualEmailAddress()
    {
        return Mage::getStoreConfig(self::MAN_EMAIL_ADDR);
    }

    public function sendManEmail()
    {
        return Mage::getStoreConfigFlag(self::SEND_ONE_EMAIL);
    }
}