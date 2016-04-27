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
 * Product Video admin widget controller
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Adminhtml_Productvideos_Video_WidgetController
    extends Mage_Adminhtml_Controller_Action {
    /**
     * Chooser Source action
     * @access public
     * @return void
     *
     */
    public function chooserAction(){
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $grid = $this->getLayout()->createBlock('blueacorn_productvideos/adminhtml_video_widget_chooser', '', array(
            'id' => $uniqId,
        ));
        $this->getResponse()->setBody($grid->toHtml());
    }
}
