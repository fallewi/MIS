<?php
$installer = $this;
$installer->startSetup();
$templateStyle = 'font-size:13px';
$subject="Mission Restaurant Supply - Exclusive Price Request";
$templateText = "";


$templateDb = Mage::getModel('core/email_template')
    ->setTemplateCode("map_request")
    ->setTemplateSubject($subject)
    ->setTemplateText($templateText)
    ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate())
    ->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML)
    ->setTemplateStyles($templateStyle)
    ->save();


$this->endSetup();