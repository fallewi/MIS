<?php

class BlueAcorn_Servicedesk_Helper_Data extends Mage_Core_Helper_Abstract {

    public function isActive()
    {
        return  (bool) Mage::getStoreConfig('admin/baservicedesk/active') && 
                (bool) Mage::getSingleton('admin/session')->isAllowed('baservicedesk');
    }

    private function getLicenseCode()
    {
        return (int) trim(Mage::getStoreConfig('admin/baservicedesk/license'));
    }

    public function getEmbedCode()
    {
$embed = <<<'livechat'
<!-- Start of LiveChat (www.livechatinc.com) code -->
<script type="text/javascript">
window.__lc = window.__lc || {};
window.__lc.license = %s;
(function() {
  var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
  lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
})();
</script>
<!-- End of LiveChat code -->
livechat;

        return sprintf($embed, (string) $this->getLicenseCode());
    }

}