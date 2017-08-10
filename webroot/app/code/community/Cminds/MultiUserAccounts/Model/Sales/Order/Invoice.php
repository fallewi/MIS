<?php

class Cminds_MultiUserAccounts_Model_Sales_Order_Invoice extends Mage_Sales_Model_Order_Invoice
{
    public function sendEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewInvoiceEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Start store emulation process
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('core/email_info');

            $helper = Mage::helper('cminds_multiuseraccounts');
            $emailTo = Mage::getStoreConfig('subAccount/general/send_copy_to_subaccount');

            switch ($emailTo) {
                case Cminds_MultiUserAccounts_Model_SubAccount_Emails::EMAIL_MASTER:
                    $emailInfo->addTo($order->getCustomerEmail(), $customerName);
                    break;

                case Cminds_MultiUserAccounts_Model_SubAccount_Emails::EMAIL_SUBACCOUNT:
                    if ($subAccountId = $order->getSubaccountId()) {
                        $subAccountData = $helper->getSubAccountData($subAccountId);
                        if ($subAccountData['get_order_invoice'] == 1) {
                            $emailInfo->addTo($subAccountData['email'], $customerName);
                        }
                    } else {
                        $emailInfo->addTo($order->getCustomerEmail(), $customerName);
                    }
                    break;

                case Cminds_MultiUserAccounts_Model_SubAccount_Emails::EMAIL_SUBACCOUNT_MASTER:
                    $emailInfo->addTo($order->getCustomerEmail(), $customerName);
                    if ($subAccountId = $order->getSubaccountId()) {
                        $subAccountData = $helper->getSubAccountData($subAccountId);
                        if ($subAccountData['get_order_invoice'] == 1) {
                            $emailInfo->addTo($subAccountData['email'], $customerName);
                        }
                    }
                    break;
            }

            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order' => $order,
                'invoice' => $this,
                'comment' => $comment,
                'billing' => $order->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            )
        );
        $mailer->send();
        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }
}