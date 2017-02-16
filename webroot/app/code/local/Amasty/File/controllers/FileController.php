<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_File
 */
class Amasty_File_FileController extends Mage_Core_Controller_Front_Action
{
    public function downloadAction()
    {
        $fileId = $this->getRequest()->getParam('file_id');
        $file = Mage::getModel("amfile/file")->load($fileId);
        if (!$file->getId()) {
            return $this->norouteAction();
        }
        Mage::getSingleton("amfile/stat")->saveStat($file->getData());
        $path = $file->getFullName();
        if(!file_exists($path)) {
            return $this->norouteAction();
        }
        $this->_prepareDownloadResponse($file->getFileName(), array(
            'type'  => 'filename',
            'value' => $path
        ));
    }

    public function testApiAction()
    {
        $api = Mage::getModel('amfile/api');

        var_dump($api->getAttachments(889,0, 58));

    }
}
