<?php
/**
 * Created by PhpStorm.
 * User: gregharvell
 * Date: 5/12/14
 * Time: 4:01 PM
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$urlKey = 'style-guide';
$rootTemplate = 'one_column';
$storeApplicable = 'default';

$installPage = $urlKey;
$currentInstallPage = Mage::getModel('cms/page')->load($installPage);

$installPageContent = <<<CONTENT
{{block type="core/template" name="style-guide" template="blueacorn/ui/styleguide.phtml"}}
CONTENT;

$installPageXML = <<<CONTENT
<reference name="head">
<action method="addItem"><type>skin_css</type><name>css/styleguide.css</name><params/></action>
<action method="addItem"><type>skin_css</type><name>css/vendor/prism.css</name><params/></action>
<action method="addItem"><type>skin_js</type><name>js/vendor/prism.js</name><params>data-manual</params></action>
<action method="addItem" ifconfig="sales/msrp/enabled"><type>skin_js</type><name>js/msrp.js</name></action>
<action method="addItem" ifconfig="sales/msrp/enabled"><type>skin_js</type><name>js/msrp_rwd.js</name></action>
<action method="addItem"><type>skin_js</type><script>js/lib/elevatezoom/jquery.elevateZoom-3.0.8.min.js</script></action>
</reference>

<reference name="cms.wrapper">
<action method="unsetChild"><name>cms_page</name></action>
</reference>

<reference name="content">
<action method="insert"><name>cms_page</name></action>
<block type="core/template" template="catalog/msrp/popup.phtml" name="product.tooltip"></block>
</reference>
CONTENT;

if($storeApplicable != ''){
    $stores = array(intval($storeApplicable));
}else{
    $stores = array(intval(0));
}

if(!$currentInstallPage->getId() || !in_array($storeApplicable, $currentInstallPage->getStoreId())){
    $cmsPages[] = array(
        'title'             =>  'Style Guide',
        'identifier'        =>  $urlKey,
        'root_template'     =>  $rootTemplate,
        'content'           =>  $installPageContent,
        'layout_update_xml' =>  $installPageXML,
        'is_active'         =>  1,
        'stores'            =>  $stores
    );

    foreach ($cmsPages as $data) {
        Mage::getModel('cms/page')->setData($data)->save();
    }

}

$installer->endSetup();
