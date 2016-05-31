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
 * Product Video widget block
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Block_Video_Widget_View
    extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface {
    protected $_htmlTemplate = 'widget/view.phtml';
    /**
     * Prepare a for widget
     * @access protected
     * @return BlueAcorn_ProductVideos_Block_Video_Widget_View
     *
     */
    protected function _beforeToHtml() {
        parent::_beforeToHtml();
        $videoId = $this->getData('video_id');
        if ($videoId) {
            $video = Mage::getModel('blueacorn_productvideos/video')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($videoId);
            if ($video->getStatus()) {
                $this->setCurrentVideo($video);
                $this->setTemplate($this->_htmlTemplate);
            }
        }
        return $this;
    }

    //add's the needed .js and .css file when trying to use product_videos.
    protected function _prepareLayout(){
        $this->getLayout()->getBlock('head')->addJs('product_videos/video-js/video.js');
        $this->getLayout()->getBlock('head')->addCss('css/video-js.css');
        return parent::_prepareLayout();
    }
}
