<?php

class BlueAcorn_Legacy_Block_Order_History extends Mage_Core_Block_Template
{

    /**
     * BlueAcorn_Legacy_Block_Order_History constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('blueacorn/legacy/order/history.phtml');

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc')
        ;

        $this->setOrders($orders);

        $legacyOrders = Mage::getResourceModel('blueacorn_legacy/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc');

        $legacyId = Mage::getSingleton('customer/session')->getCustomer()->getLegacyCustomerId();

        if ($legacyId) {
            $legacyOrders->addFieldToFilter('customer_id', $legacyId);
        } else {
            $legacyOrders->addFieldToFilter('entity_id', 0);
        }

        $this->setLegacyOrders($legacyOrders);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('My Orders'));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'legacy.order.history.pager')
            ->setCollection($this->getOrders());
        $this->setChild('pager', $pager);
        $this->getOrders()->load();
        return $this;
    }

    /**
     * Gets Pager HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Gets view link for order
     *
     * @param $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
    }

    /**
     * Gets legacy order link
     *
     * @param $order
     * @return string
     */
    public function getLegacyViewUrl($order)
    {
        return $this->getUrl('legacy/*/view', array('order_id' => $order->getId()));
    }

    /**
     * Gets order track url
     *
     * @param $order
     * @return string
     */
    public function getTrackUrl($order)
    {
        return $this->getUrl('*/*/track', array('order_id' => $order->getId()));
    }

    /**
     * Gets reorder url
     *
     * @param $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('*/*/reorder', array('order_id' => $order->getId()));
    }

    /**
     * Gets Back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
