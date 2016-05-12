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
 * Product Video helper
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Helper_Video
    extends Mage_Core_Helper_Abstract {
    /**
     * get base files dir
     * @access public
     * @return string
     *
     */
    public function getFileBaseDir(){
        return Mage::getBaseDir('media').DS.'video'.DS.'file';
    }

    /**
     * get base thumbnail dir
     * @access public
     * @return string
     *
     */
    public function getThumbBaseDir(){
        return Mage::getBaseDir('media').DS.'video'.DS.'thumbnails';
    }
    /**
     * get base file url
     * @access public
     * @return string
     *
     */
    public function getFileBaseUrl(){
        return Mage::getBaseUrl('media').'video'.'/'.'file';
    }

    /**
     * get base thumbnail url
     * @access public
     * @return string
     *
     */
    public function getThumbBaseUrl(){
        return Mage::getBaseUrl('media').'video'.'/'.'thumbnails';
    }
}
