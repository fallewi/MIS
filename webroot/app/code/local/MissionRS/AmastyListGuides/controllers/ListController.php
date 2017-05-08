<?php
/**
 * @author MissionRS Victor Cortez
 * @package MissionRS_AmastyListGuides
 * Updated Parent Core files $_customerId and  _renderLayoutWithMenu()
 * setting them as protected to avoid rewriting.
 */
require_once(Mage::getModuleDir('controllers','Amasty_List').DS.'ListController.php');

class MissionRS_AmastyListGuides_ListController extends Amasty_List_ListController
{

    /**
     * Show list's title and items for order
     * modified editAction
     */
    public function orderAction()
    {
        $list = Mage::getModel('amlist/list');
        $id = $this->getRequest()->getParam('id');
        if ($id){
             $list->load($id);             
             $sharedListIds = Mage::helper('amlistl')->getSharedListIds($this->_customerId);               
             $sharedListIds[] = $list->getCustomerId();
           
            if (!in_array($list->getCustomerId(), $sharedListIds)){
                $this->_redirect('*/*/');
                return;
            }
        }
        Mage::register('current_list', $list);

        $this->_renderLayoutWithMenu();
    }


    /**
     * MRS send list's items to cart without checkboxes and qtys
     * modified updateAction added parts of cartAction
     */
    public function mrscartAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/');
            return;
        }
       
        $listId = $this->getRequest()->getParam('list_id');
 
        $list  = Mage::getModel('amlist/list');
        $list->load($listId);
        $sharedListIds = Mage::helper('amlistl')->getSharedListIds($this->_customerId);
        $sharedListIds[] = $list->getCustomerId();

        if (!in_array($list->getCustomerId(), $sharedListIds)) {
            $this->_redirect('*/*/');
            return;
        }

        $post = $this->getRequest()->getPost();
        
        if ($post && isset($post['qty']) && is_array($post['qty']))
        {
            $quote = Mage::getSingleton('checkout/cart'); // create a cart quote
            foreach ($post['qty'] as $itemId => $qty) {
                $item = Mage::getModel('amlist/item')->load($itemId);
                if ($item->getListId() != $listId) {
                    continue;
                }
                try {
                    if ($qty)
                    {
                        $product = Mage::getModel('catalog/product')
                            ->load($item->getProductId())
                            ->setQty(max(0.01, intVal($qty)));

                        $req = unserialize($item->getBuyRequest());
                        $req['qty'] = intVal($qty);

                        $quote->addProduct($product, $req);



                    }
                }
                catch (Exception $e) {
                    Mage::getSingleton('customer/session')->addError(
                        $this->__('Can not save item: %s.', $e->getMessage())
                    );
                }
            }
            $quote->save();
        }
        $this->_redirect('checkout/cart');
    }
    
    public function exportCsvAction()
    {
        $exportData = Mage::helper('amlistl')->exportGuides();
        $this->_prepareDownloadResponse("order_guide_{$this->_customerId}.csv", $exportData);
    }

    public function importCsvAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            try {
                /**
                 * @var $helper Amasty_List_Helper_Data
                 */
                $helper = Mage::helper('amlist/data');
                /**
                 * @var $listModel Amasty_List_Model_List
                 */
                $listModel = Mage::getModel('amlist/list');

                //Init Uploader
                try {
                    $uploader = new Varien_File_Uploader('file');
                    $uploader->setAllowedExtensions(array('csv'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);

                    $folderPath = Mage::getBaseDir('media') . $helper::CSV_FOLDER_PATH;
                    $fileName = 'import_' . date('Y-m-d') . '.csv';

                    // Upload the file
                    $uploader->save($folderPath, $fileName);
                } catch (\Exception $e) {
                    throw new Exception($this->__('check uploaded file'));
                }

                $fileName = $uploader->getUploadedFileName();
                $productData = $helper->parseProductCsv($fileName);                
                Mage::getModel('amlistl/list')->createListFromCsv($productData, $this->_customerId);
                Mage::getSingleton('customer/session')->addSuccess('Import from CSV was successfully finished');
              
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError(
                        $this->__('There was an error while uploading CSV: %s', $e->getMessage())
                );
            }
        }

        $this->_redirect('*/*/index');
    }
}
