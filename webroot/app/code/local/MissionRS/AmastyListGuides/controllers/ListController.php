<?php

require_once 'Amasty/List/controllers/ListController.php' ;

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
            if ($list->getCustomerId() != $this->_customerId){
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
        if ($list->getCustomerId() != $this->_customerId){
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
}
