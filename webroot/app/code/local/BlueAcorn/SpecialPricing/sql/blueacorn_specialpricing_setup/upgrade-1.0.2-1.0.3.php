<?php
$installer = $this;
$installer->startSetup();
$templateText = '<!--@subject Welcome, {{var customer.name}}! @-->
<!--@vars
{"store url=\"\"":"Store Url",
"var logo_url":"Email Logo Image Url",
"htmlescape var=$customer.name":"Customer Name",
"store url=\"customer/account/\"":"Customer Account Url",
"var customer.email":"Customer Email",
"htmlescape var=$customer.password":"Customer Password"}
@-->

<!--@styles
@-->

{{template config_path="design/email/header"}}
{{inlinecss file="email-inline.css"}}

<table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td class="action-content">
            <h1>Hello!</h1>
            <p>Thank you for your interest in the <strong style="font-weight: 700;">{{var productName}}</strong></p>
            <p>This item is currently available for you at MissionRS.com for <strong style="font-weight: 700;">an exclusive, discounted price of {{var price}}.</strong></p>
            <div class="product-container" style="margin-bottom: 20px;">
                <img src="${{var productImage}}">
                <div class="product-info" style="float:left; width: 310px; word-wrap: break-word; background: #f9f9f9; border: 1px solid #e0e0e0; padding: 10px 20px;">
                    <p style="margin: 0;"><strong style="font-weight: 700;">{{var manufacturer}}</strong></p>
                    <p style="margin: 0;"><strong style="font-weight: 700;">{{var productName}}</strong></p>
                    <p style="margin: 0;"><strong style="font-weight: 700;">Product Token: </strong>{{var token}}</p>
                    </br>
                    <p><strong style="font-weight: 700;">Your Price: </strong>{{var price}}</p>
                    <p style="margin: 0;"><strong style="font-weight: 700;">Add to Cart (copy the URL and paste it into your browser):</strong></p>
                    <a href="{{var link}}">{{var link}}</a>
                </div>
                <div style="clear: both;"></div>
            </div>
            <small>Please note: this activation is for one-time use only and is only valid today. The item will be added to your cart with the discount applied. You can remove it from your cart at any time. Your email address is safe. We do not collect or share your information with anyone else.</small>
            <p>
                If you have any questions about your account or any other matter, please feel free to contact us at
                <a href="mailto:{{var store_email}}">{{var store_email}}</a>
                {{depend store_phone}} or by phone at <a href="tel:{{var phone}}">{{var store_phone}}</a>{{/depend}} from Monday to Friday 7 a.m.  6 p.m. Central.
            </p>
        </td>
    </tr>
</table>

{{template config_path="design/email/footer"}}';

$emailTemplate = Mage::getModel('core/email_template')
    ->getCollection()
    ->addFieldToFilter('template_code', 'map_request')
    ->getFirstItem()
    ->setTemplateText($templateText)
    ->save();

$this->endSetup();
