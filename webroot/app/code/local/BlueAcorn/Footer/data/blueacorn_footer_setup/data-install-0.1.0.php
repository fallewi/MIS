<?php
/**
 * @package BlueAcorn_Footer
 * @version 0.2.0
 * @author Tyler Craft
 * @copyright Copyright (c) 2015 Blue Acorn, Inc.
 */
$installer = $this;

$installer->startSetup();

$content = <<<CONTENT
<div class="tablet-links">
<div class="links">
<h5 class="block-title">Company Info</h5>
<ul>
<li><a href="{{store url=""}}">MRS Blog</a></li>
<li><a href="{{store url=""}}">Store Locations</a></li>
<li><a href="{{store url=""}}">Career Opportunities</a></li>
<li><a href="{{store url=""}}">Affiliate Marketing</a></li>
</ul>
</div>
<div class="links">
<h5 class="block-title">Policies</h5>
<ul>
<li><a href="{{store url=""}}">Terms of Use</a></li>
<li><a href="{{store url=""}}">Shipping Policies</a></li>
<li><a href="{{store url=""}}">Return Policies</a></li>
<li><a href="{{store url=""}}">Privacy Policies</a></li>
</ul>
</div>
</div>
<div class="tablet-links">
<div class="links">
<h5 class="block-title">Customer Service</h5>
<ul>
<li><a href="{{store url=""}}">Customer Service</a></li>
<li><a href="{{store url=""}}">Contact Us</a></li>
<li><a href="{{store url=""}}">Request a Catalog</a></li>
<li><a href="{{store url=""}}">More Ways to Pay</a></li>
<li><a href="{{store url=""}}">Request a Quote</a></li>
<li><a href="{{store url=""}}">Find Coupons</a></li>
<li><a href="{{store url=""}}">Popular Searches</a></li>
<li><a href="{{store url=""}}">Sitemap</a></li>
</ul>
</div>
<div class="links">
<h5 class="block-title">My Account</h5>
<ul>
<li><a href="{{store url=""}}">Login/Register</a></li>
<li><a href="{{store url=""}}">Order Status</a></li>
</ul>
<h5 class="block-title">Connect</h5>
<ul>
<li><a href="{{store url=""}}">Facebook</a></li>
<li><a href="{{store url=""}}">Twitter</a></li>
</ul>
</div>
</div>
CONTENT;


$now = Mage::app()->getLocale()->date()
    ->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)
    ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

$block = array(
    'title' => 'Footer Links',
    'identifier' => 'footer_links_company',
    'content' => $footerContent,
    'creation_time' => $now,
    'update_time' => $now,
    'is_active' => 1,
    'stores' => 0
);

try {
    Mage::getModel('cms/block')->setData($block)->save();
} catch(Exception $e){
    Mage::logException($e);
}


$installer->endSetup();