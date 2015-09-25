<?php
$installer = $this;
$installer->startSetup();
$templateStyle = 'font-size:13px';
$subject="Mission Restaurant Supply - Exclusive Price Request";
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
            <h1 style="font-size: 18px; margin-bottom: 15px; line-height: 18px;">Hello,</h1>
            <p>Thank you for your interest in the <strong style="font-weight: 700;">Product Name</strong></p>
            <p>This item is currently available for you at MissionRS.com for <strong style="font-weight: 700;">an exclusive, discounted price of $2,814.55.</strong></p>
            <div class="product-container" style="margin-bottom: 20px;">
                <img src="http://placekitten.com/g/188/188" style="float:left; margin-right: 5px; width: 190px; border: 1px solid #e0e0e0;">
                <div class="product-info" style="float:left; width: 310px; word-wrap: break-word; background: #f9f9f9; border: 1px solid #e0e0e0; padding: 10px 20px;">
                    <p style="margin: 0;"><strong style="font-weight: 700;">Manufacturing Company Name</strong></p>
                    <p style="margin: 0;"><strong style="font-weight: 700;">Product Name</strong></p>
                    <p style="margin: 0;"><strong style="font-weight: 700;">Product Token: </strong>065D82F6-FBF2-E23F-0C3E-4A51B4E92C6D</p>
                    </br>
                    <p><strong style="font-weight: 700;">Your Price: </strong>$2,814.55</p>
                    <p style="margin: 0;"><strong style="font-weight: 700;">Add to Cart (copy the URL and paste it into your browser):</strong></p>
                    <a href="#">http://www.missionrs.com/levementum_custommapp/index/confirm/product_id/4633/token_id/065D82F6-FBF2-E23F-0C3E-4A51B4E92C6D/email/sarah.steen@blueacorn.com/options/N;/type/simple/</a>
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


$templateDb = Mage::getModel('core/email_template')
    ->setTemplateCode("map_request")
    ->setTemplateSubject($subject)
    ->setTemplateText($templateText)
    ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate())
    ->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML)
    ->setTemplateStyles($templateStyle)
    ->save();


$this->endSetup();