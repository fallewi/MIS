<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_File
 */

class Amasty_File_Adminhtml_Amfile_FileController extends Mage_Adminhtml_Controller_Action
{
    public function downloadAction()
    {
        $fileId = $this->getRequest()->getParam('file_id');
        $file = Mage::getModel("amfile/file")->load($fileId);
        if (!$file->getId()) {
            return $this->norouteAction();
        }
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

    public function updateAction()
    {
        $result = Mage::getSingleton('amfile/observer')->updateFileData();

        $productId = Mage::app()->getRequest()->getParam('id');
        $storeId = Mage::app()->getRequest()->getParam('store', 0);

        $files = Mage::getResourceModel('amfile/file_collection')->getFilesAdmin($productId, $storeId);

        $files->getSelect()
            ->where('main_table.file_id IN (?)', $result['updated']);

        $content = '';
        foreach ($files as $file)
        {
            $block = new Mage_Core_Block_Template();

            $content .= $block
                ->setTemplate('amfile/tab/item.phtml')
                ->setStoreId($storeId)
                ->setItem($file)
                ->toHtml();
        }

        $this->getAnswer(array_values($result['errors']), $content);
    }

    protected function getAnswer($errors, $content = '')
    {
        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode(
                array(
                    'errors' => $errors,
                    'content' => $content,
                )
            )
        );
    }

    public function updateGridAction() {

        $result = Mage::getSingleton('amfile/observer')->updateFileData(true);

        $storeId = Mage::app()->getStore()->getId();
        $productId = Mage::app()->getRequest()->getPost('amfile_product');
        $files = Mage::getResourceModel('amfile/file_collection')->getFilesAdmin($productId, $storeId);

        $block = $this->getLayout()->createBlock('amfile/adminhtml_renderer_attachments');
        $block->setData('files', $files);
        $block->setData('product_id', $productId);

        $content = $block
            ->setTemplate('amfile/attached_files.phtml')
            ->toHtml();

        $this->getAnswer(
            array_values($result['errors']),
            array('content' => $content, 'product_id' => $productId)
        );

    }

    protected function _isAllowed()
    {
        return true;
    }

}
