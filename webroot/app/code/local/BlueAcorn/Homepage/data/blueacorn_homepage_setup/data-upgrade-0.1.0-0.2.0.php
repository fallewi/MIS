<?php
/**
 * @package BlueAcorn_Homepage
 * @version 0.2.0
 * @author Tyler Craft
 * @copyright Copyright (c) 2015 Blue Acorn, Inc.
 */
$installer = $this;

$installer->startSetup();

$sliderContent = <<<CONTENT
<div class="hero-container">
    <div class="hero-img">
        <img class="desktop-image" alt="" src="{{media url="wysiwyg/Artboard_19.jpg"}}" />
        <img class="mobile-image" alt="" src="{{media url="wysiwyg/Artboard_20.jpg"}}" />
    </div>
    <div class="hero-content">
        <h2>Vitamix Barboss Advanced Blender</h2>
        <p>This variable speed Vita-Prep 3 (1005) Commercial Food Blender from Vitamix &reg; is a powerful.</p>
        <a class="button btn-cart" href="#">Shop Vitamix Now</a>
    </div>
</div>
CONTENT;


$now = Mage::app()->getLocale()->date()
    ->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)
    ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

$slider = array(
    'title' => 'Homepage Hero',
    'identifier' => 'homepage_hero',
    'content' => $sliderContent,
    'creation_time' => $now,
    'update_time' => $now,
    'is_active' => 1,
    'stores' => 0
);

try {
    Mage::getModel('cms/block')->setData($slider)->save();
} catch(Exception $e){
    Mage::logException($e);
}


$installer->endSetup();