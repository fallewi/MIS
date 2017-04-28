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

        if (Mage::getStoreConfig('amfile/additional/detect_mime')) {
            $mimeType = Mage::helper('amfile')->getMimeType($path);
            $this->getResponse()
                ->setHeader('Content-Description', 'File Transfer', true)
                ->setHeader('Content-Type', $mimeType, true)
                ->setHeader('Content-Disposition', 'inline; filename="' . $file->getFileName() . '"', true)
                ->setHeader('Content-Transfer-Encoding', 'binary', true)
                ->setHeader('Expires', '0', true)
                ->setHeader('Cache-Control', 'must-revalidate', true)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-Length', filesize($path))
                ->setBody(file_get_contents($path))
                ->sendResponse()
            ;
            Mage::helper('ambase/utils')->_exit();
        } else {
            $this->_prepareDownloadResponse($file->getFileName(), array(
                'type'  => 'filename',
                'value' => $path
            ));
        }
    }

    public function testApiAction()
    {
        $api = Mage::getModel('amfile/api');

        var_dump($api->getAttachments(889,0, 58));

    }
}
