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
 * Product Video admin controller
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Adminhtml_Productvideos_VideoController
    extends BlueAcorn_ProductVideos_Controller_Adminhtml_ProductVideos {
    /**
     * init the video
     * @access protected
     * @return BlueAcorn_ProductVideos_Model_Video
     */
    protected function _initVideo(){
        $videoId  = (int) $this->getRequest()->getParam('id');
        $video    = Mage::getModel('blueacorn_productvideos/video');
        if ($videoId) {
            $video->load($videoId);
        }
        Mage::register('current_video', $video);
        return $video;
    }
    /**
     * default action
     * @access public
     * @return void
     *
     */
    public function indexAction() {
        $this->loadLayout();
        $this->_title(Mage::helper('blueacorn_productvideos')->__('Product Videos'))
            ->_title(Mage::helper('blueacorn_productvideos')->__('Product Videos'));
        $this->renderLayout();
    }
    /**
     * grid action
     * @access public
     * @return void
     *
     */
    public function gridAction() {
        $this->loadLayout()->renderLayout();
    }
    /**
     * edit product video - action
     * @access public
     * @return void
     *
     */
    public function editAction() {
        $videoId    = $this->getRequest()->getParam('id');
        $video      = $this->_initVideo();
        if ($videoId && !$video->getId()) {
            $this->_getSession()->addError(Mage::helper('blueacorn_productvideos')->__('This product video no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getVideoData(true);
        if (!empty($data)) {
            $video->setData($data);
        }
        Mage::register('video_data', $video);
        $this->loadLayout();
        $this->_title(Mage::helper('blueacorn_productvideos')->__('Product Videos'))
            ->_title(Mage::helper('blueacorn_productvideos')->__('Product Videos'));
        if ($video->getId()){
            $this->_title($video->getTitle());
        }
        else{
            $this->_title(Mage::helper('blueacorn_productvideos')->__('Add product video'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }
    /**
     * new product video action
     * @access public
     * @return void
     *
     */
    public function newAction() {
        $this->_forward('edit');
    }
    /**
     * save product video - action
     * @access public
     * @return void
     *
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost('video')) {

            if(is_dir_writeable(Mage::getBaseDir('media')))
            {
                try {
                    $video = $this->_initVideo();
                    $video->addData($data);
                    $fileName = $this->_uploadAndGetName('file', Mage::helper('blueacorn_productvideos/video')->getFileBaseDir(), $data);
                    $video->setData('file', $fileName);
                    $thumbnailName = $this->_uploadAndGetName('thumbnail', Mage::helper('blueacorn_productvideos/video')->getThumbBaseDir(), $data);

                    $thumbnailName = Mage::getBaseUrl('media') . $thumbnailName;
                    $thumbnailName = str_replace(Mage::helper('blueacorn_productvideos/video')->getThumbBaseUrl(), '',$thumbnailName);
                    $thumbnailName = str_replace(Mage::helper('blueacorn_productvideos/video')->getThumbBaseDir(), '',$thumbnailName);
                    $thumbnailName = str_replace(Mage::getBaseUrl('media'), '',$thumbnailName);
                    $video->setData('thumbnail', Mage::helper('blueacorn_productvideos/video')->getThumbBaseUrl() . $thumbnailName);

                    Mage::helper('blueacorn_productvideos/product')->setThumbnail($video);
                    $products = $this->getRequest()->getPost('products', -1);
                    if ($products != -1) {
                        $video->setProductsData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($products));
                    }
                    $video->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('blueacorn_productvideos')->__('Product Video was successfully saved'));
                    Mage::getSingleton('adminhtml/session')->setFormData(false);
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $video->getId()));
                        return;
                    }
                    $this->_redirect('*/*/');
                    return;
                }
                catch (Mage_Core_Exception $e){
                    if (isset($data['file']['value'])){
                        $data['file'] = $data['file']['value'];
                    }
                    if (isset($data['thumbnail']['value'])){
                        $data['thumbnail'] = $data['thumbnail']['value'];
                    }
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setVideoData($data);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
                catch (Exception $e) {
                    Mage::logException($e);
                    if (isset($data['file']['value'])){
                        $data['file'] = $data['file']['value'];
                    }
                    if (isset($data['thumbnail']['value'])){
                        $data['thumbnail'] = $data['thumbnail']['value'];
                    }
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blueacorn_productvideos')->__('There was a problem saving the product video. Please be sure that video format is correct'));
                    Mage::getSingleton('adminhtml/session')->setVideoData($data);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
            }
            else
            {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blueacorn_productvideos')->__('Your Media folders permission is not "777", please change it and try again.'));
                $this->_redirect('*/*/');
            }

        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blueacorn_productvideos')->__('Unable to find product video to save.'));
        $this->_redirect('*/*/');
    }
    /**
     * delete product video - action
     * @access public
     * @return void
     *
     */
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0) {
            try {
                $video = Mage::getModel('blueacorn_productvideos/video');
                $video->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('blueacorn_productvideos')->__('Product Video was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blueacorn_productvideos')->__('There was an error deleting product video.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blueacorn_productvideos')->__('Could not find product video to delete.'));
        $this->_redirect('*/*/');
    }
    /**
     * mass delete product video - action
     * @access public
     * @return void
     *
     */
    public function massDeleteAction() {
        $videoIds = $this->getRequest()->getParam('video');
        if(!is_array($videoIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blueacorn_productvideos')->__('Please select product videos to delete.'));
        }
        else {
            try {
                foreach ($videoIds as $videoId) {
                    $video = Mage::getModel('blueacorn_productvideos/video');
                    $video->setId($videoId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('blueacorn_productvideos')->__('Total of %d product videos were successfully deleted.', count($videoIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blueacorn_productvideos')->__('There was an error deleting product videos.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * mass status change - action
     * @access public
     * @return void
     *
     */
    public function massStatusAction(){
        $videoIds = $this->getRequest()->getParam('video');
        if(!is_array($videoIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blueacorn_productvideos')->__('Please select product videos.'));
        }
        else {
            try {
                foreach ($videoIds as $videoId) {
                    $video = Mage::getSingleton('blueacorn_productvideos/video')->load($videoId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d product videos were successfully updated.', count($videoIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blueacorn_productvideos')->__('There was an error updating product videos.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * get grid of products action
     * @access public
     * @return void
     *
     */
    public function productsAction(){
        $this->_initVideo();
        $this->loadLayout();
        $this->getLayout()->getBlock('video.edit.tab.product')
            ->setVideoProducts($this->getRequest()->getPost('video_products', null));
        $this->renderLayout();
    }
    /**
     * get grid of products action
     * @access public
     * @return void
     *
     */
    public function productsgridAction(){
        $this->_initVideo();
        $this->loadLayout();
        $this->getLayout()->getBlock('video.edit.tab.product')
            ->setVideoProducts($this->getRequest()->getPost('video_products', null));
        $this->renderLayout();
    }
    /**
     * export as csv - action
     * @access public
     * @return void
     *
     */
    public function exportCsvAction(){
        $fileName   = 'video.csv';
        $content    = $this->getLayout()->createBlock('blueacorn_productvideos/adminhtml_video_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as MsExcel - action
     * @access public
     * @return void
     *
     */
    public function exportExcelAction(){
        $fileName   = 'video.xls';
        $content    = $this->getLayout()->createBlock('blueacorn_productvideos/adminhtml_video_grid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as xml - action
     * @access public
     * @return void
     *
     */
    public function exportXmlAction(){
        $fileName   = 'video.xml';
        $content    = $this->getLayout()->createBlock('blueacorn_productvideos/adminhtml_video_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * Check if admin has permissions to visit related pages
     * @access protected
     * @return boolean
     *
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/blueacorn_productvideos/video');
    }

    /**
     * Changes the media folders permission to 777 so that uploads can be made by the client.
     * @return bool
     */
    protected function _changeFolderPermission()
    {
        return chmod(Mage::getBaseDir('media'), 0777);
    }
}
