<?php

require_once 'Mage/Checkout/controllers/CartController.php';

class Cminds_MultiUserAccounts_SubcartController
    extends Mage_Checkout_CartController
{

    /**
     * Delete shoping cart item action
     */
    public function deleteItemAction()
    {
        if ($this->_validateFormKey()) {
            $id = (int)$this->getRequest()->getParam('id');
            $quoteId = (int)$this->getRequest()->getParam('quote_id');
            if ($id && $quoteId) {
                try {
                    $cart = Mage::getModel('sales/quote')->load($quoteId);
                    $cart->removeItem($id)
                        ->save();
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__('Cannot remove the item.'));
                    Mage::logException($e);
                }
            }
        } else {
            $this->_getSession()->addError($this->__('Cannot remove the item.'));
        }

        $this->_redirectReferer(Mage::getUrl('*/*'));
    }

    public function updateCartPostAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirectReferer(Mage::getUrl('*/*'));
            return;
        }

        $this->updateShoppingCart();


        $this->_redirectReferer(Mage::getUrl('*/*'));
        return;
    }

    /**
     * Update customer's shopping cart
     */
    protected function updateShoppingCart()
    {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            $quoteId = $this->getRequest()->getParam('quote_id');
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = Mage::getModel('sales/quote')->load($quoteId);
                $this->updateItems($cart, $cartData);
                $cart->save();
            }
            $this->_getSession()->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
            Mage::logException($e);
        }
    }

    public function updateItems($cart, $cartData)
    {
        /* @var $messageFactory Mage_Core_Model_Message */
        $messageFactory = Mage::getSingleton('core/message');
        $session = Mage::getSingleton('checkout/session');
        $qtyRecalculatedFlag = false;
        foreach ($cartData as $itemId => $itemInfo) {
            $item = $cart->getItemById($itemId);
            if (!$item) {
                continue;
            }

            if (!empty($itemInfo['remove']) || (isset($itemInfo['qty']) && $itemInfo['qty'] == '0')) {
                $this->removeItem($itemId);
                continue;
            }

            $qty = isset($itemInfo['qty']) ? (float)$itemInfo['qty'] : false;
            if ($qty > 0) {
                $item->setQty($qty);
                $item->save();
                $itemInQuote = $cart->getItemById($item->getId());

                if (!$itemInQuote && $item->getHasError()) {
                    Mage::throwException($item->getMessage());
                }

                if (isset($itemInfo['before_suggest_qty']) && ($itemInfo['before_suggest_qty'] != $qty)) {
                    $qtyRecalculatedFlag = true;
                    $message = $messageFactory->notice(Mage::helper('checkout')->__('Quantity was recalculated from %d to %d',
                        $itemInfo['before_suggest_qty'], $qty));
                    $session->addQuoteItemMessage($item->getId(), $message);
                }
            }
        }

        if ($qtyRecalculatedFlag) {
            $session->addNotice(
                Mage::helper('checkout')->__('Some products quantities were recalculated because of quantity increment mismatch')
            );
        }

        return $this;
    }
}
