<?php
/**
 * BlueAcorn_ProductVideos extension
 * 
 *
 * @category       BlueAcorn
 * @package        BlueAcorn_ProductVideos
 * @author         Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright      Copyright © 2014 Blue Acorn, Inc.
 */
/**
 * ProductVideos default helper
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Helper_Data
    extends Mage_Core_Helper_Abstract {
    /**
     * convert array to options
     * @access public
     * @param $options
     * @return array
     *
     */
    public function convertOptions($options){
        $converted = array();
        foreach ($options as $option){
            if (isset($option['value']) && !is_array($option['value']) && isset($option['label']) && !is_array($option['label'])){
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }
}
