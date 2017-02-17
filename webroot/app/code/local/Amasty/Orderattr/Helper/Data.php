<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */
class Amasty_Orderattr_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_orderData = false;

    public function fields($step)
    {
        return Mage::app()->getLayout()->createBlock('amorderattr/fields')->setStep($step)->toHtml();
    }

    public function field($code)
    {
        return Mage::app()->getLayout()->createBlock('amorderattr/fields')->setAttributeCode($code)->toHtml();
    }
    
    //$object is a "$this" from template
    public function express($step, $object)
    {
        if ('address' == $step){
            if ($object->getShowAsShippingCheckbox())
                return Mage::app()->getLayout()->createBlock('amorderattr/fields')->setStep('billing')->toHtml();
            else
                return Mage::app()->getLayout()->createBlock('amorderattr/fields')->setStep('shipping')->toHtml();
        }
        return Mage::app()->getLayout()->createBlock('amorderattr/fields')->setStep($step)->toHtml(); 
    }
   
    public function clearCache()
    {
        $cacheDir = Mage::getBaseDir('var') . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
        $this->_clearDir($cacheDir);
        Mage::app()->cleanCache();
        Mage::getConfig()->reinit();

      //the client had a case when needed flush Cache Storage
      //Mage::app()->getCacheInstance()->flush();
    }
    
    protected function _clearDir($dir = '')
    {
        if($dir) 
        {
            if (is_dir($dir)) 
            {
                if ($handle = @opendir($dir)) 
                {
                    while (($file = readdir($handle)) !== false) 
                    {
                        if ($file != "." && $file != "..") 
                        {
                            $fullpath = $dir . '/' . $file;
                            if (is_dir($fullpath)) 
                            {
                                $this->_clearDir($fullpath);
                                @rmdir($fullpath);
                            }
                            else 
                            {
                                @unlink($fullpath);
                            }
                        }
                    }
                    closedir($handle);
                }
            }
        }
    }
    
    public function getAttributeValue($attribute, $order = null)
    {
        $attributeValue = '';
        $attributeCode = $attribute->getAttributeCode();
        if ($order) {
            $orderData = $this->_getOrderData($order->getId());
            if (isset($orderData[$attributeCode])) {
                $attributeValue = $orderData[$attributeCode];
            }
        } elseif ($attribute->getData('default_value')) {
           $attributeValue = $attribute->getData('default_value');
        }
        $session = Mage::getSingleton('checkout/session');
        $orderAttributes = $session->getAmastyOrderAttributes();
         
        if (is_array($orderAttributes) && array_key_exists($attributeCode, $orderAttributes)){
           $attributeValue = $orderAttributes[$attributeCode];
            $inputType = $attribute->getFrontend()->getInputType();
            if('checkboxes' == $inputType){
               $attributeValue = implode(',',$attributeValue);
            }
        }
        else{
           // if enabled, we will pre-fill the attribut with the last used value. works for registered customer only
           if ($attribute->getSaveSelected() && Mage::getSingleton('customer/session')->isLoggedIn())
           {
               $orderCollection = Mage::getModel('sales/order')->getCollection();
               $orderCollection->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getId());
               // 1.3 compatibility
               $alias = Mage::helper('ambase')->isVersionLessThan(1,4) ? 'e' : 'main_table';
               $orderCollection->getSelect()
                  ->join(
                       array('custom_attributes' => Mage::getModel('amorderattr/attribute')->getResource()->getTable('amorderattr/order_attribute')),
                       "$alias.entity_id = custom_attributes.order_id",
                       array($attributeCode)
                  );
               $orderCollection->getSelect()->order('custom_attributes.order_id DESC');
               $orderCollection->getSelect()->limit(1);
               if ($orderCollection->getSize() > 0)
               {
                   foreach ($orderCollection as $lastOrder)
                   {
                       $attributeValue = $lastOrder->getData($attributeCode);
                   }
               }
           }
        }
        return $attributeValue;
    }
    
    public function getShippingMethods($attributeId){
        $model = Mage::getModel('amorderattr/shipping_methods');
        $collection = $model->getCollection();
        
        $collection->addFilter('attribute_id', $attributeId);
        $collection->load();
        return $collection->getItems();
        
    }
    
    public function getDateTimeFormat()
    {
        if (!Mage::getStoreConfig('amorderattr/general/default_format')) {
            return Mage::getStoreConfig('amorderattr/general/datetime_format');
        }
        return Mage::app()->getLocale()->getDateTimeFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
        );
    }

    public function getDownloadFileUrl($orderId, $attributeCode) {
        if (Mage::app()->getStore()->isAdmin()) {
            return $this->_getUrl(
                'adminhtml/amorderattrfiledownload/download',
                array('code' => Mage::helper('core')->urlEncode($attributeCode), 'order' => $orderId)
            );
        }
        $order = Mage::getModel('sales/order')->load($orderId);
        return Mage::getModel('core/url')->getUrl(
            'amorderattr/file/download',
            array('code' => Mage::helper('core')->urlEncode($attributeCode), 'order' => $orderId, '_store'=> $order->getStoreId())
        );
    }

    protected function _getOrderData($orderId)
    {
        if ($this->_orderData === false) {
            $orderAttributes  = Mage::getModel('amorderattr/attribute');
            $this->_orderData = $orderAttributes
                ->load($orderId, 'order_id')
                ->getData();
        }
        return $this->_orderData;
    }

    public function normalizeString ($str = '')
    {
        $str = strip_tags($str);
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtolower($str);
        $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '-', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '-', $str);

        return $str;
    }
}
