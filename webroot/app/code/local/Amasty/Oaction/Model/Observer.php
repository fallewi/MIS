<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Oaction
 */


class Amasty_Oaction_Model_Observer
{
    public function addNewActions($observer)
    {
        if (!$this->_isSalesGrid($observer->getPage())) {
            return $this;
        }

        $block = $observer->getBlock();

        if ('Enterprise' == Mage::getEdition()
            && Mage::helper('core')->isModuleEnabled('Enterprise_SalesArchive')
        ) {
            $isActive = Mage::getSingleton('enterprise_salesarchive/config')->isArchiveActive();
            if ($isActive && Mage::getSingleton('admin/session')->isAllowed('sales/archive/order/add')) {
                $block->addItem('add_order_to_archive', array(
                     'label'=> Mage::helper('enterprise_salesarchive')->__('Move to Archive'),
                     'url'  => Mage::helper('adminhtml')->getUrl('adminhtml/sales_archive/massAdd'),
                ));
            }
        }

        $types = Mage::getStoreConfig('amoaction/general/commands');
        if (!$types) {
            return $this;
        }

        $types = explode(',', $types);
        foreach ($types as $i => $type) {
            if ($type) {
                $command = Amasty_Oaction_Model_Command_Abstract::factory($type);
                $command->addAction($block);
            } else { // separator
                $block->addItem('amoaction_separator' . $i, array(
                    'label'=> '---------------------',
                    'url'  => ''
                ));
            }
        }

        if ($this->isExtensionActive('SLandsbek_SimpleOrderExport')) {
            $block->addItem('amoaction_separator' . $i, array(
                'label'=> '---------------------',
                'url'  => ''
            ));
            $block->addItem('simpleorderexport', array(
                'label' => 'Export to .csv file',
                'url' => Mage::app()->getStore()->getUrl('simpleorderexport/export_order/csvexport'),
            ));
        }

        return $this;
    }

    public function modifyJs($observer)
    {
        $page = $observer->getResult()->getPage();
        if (!$this->_isSalesGrid($page)) {
            return $this;
        }

        $js = $observer->getResult()->getJs();
        $js = str_replace('varienGridMassaction', 'amoaction', $js);
        $observer->getResult()->setJs($js);

        return $this;
    }

    protected function _isSalesGrid($page)
    {
	   return in_array($page, array('adminhtml_sales_order', 'sales_order', 'orderspro_order'));
    }

    protected function isExtensionActive($extensionName)
    {
        $val = Mage::getConfig()->getNode('modules/' . $extensionName . '/active');
	    return ((string)$val == 'true');
    }

    public function modifyOrderGridAfterBlockGenerate($observer)
    {
        $permissibleActions = array('index', 'grid', 'exportCsv', 'exportExcel');
        $exportActions = array('exportCsv', 'exportExcel');

        if (false === strpos(Mage::app()->getRequest()->getControllerName(), 'sales_order')
            || !in_array(Mage::app()->getRequest()->getActionName(), $permissibleActions)
        ) {
            return;
        }

        $export = in_array(Mage::app()->getRequest()->getActionName(), $exportActions);

        $block = $observer->getBlock();

		$blockClass = Mage::getConfig()->getBlockClassName('adminhtml/sales_order_grid');
        if ($blockClass == get_class($block)
			&& Mage::getStoreConfig('amoaction/ship/addcolumn')
        ) {
            $block->addColumnAfter('amoaction_shipping', array(
                'header' => Mage::helper('amoaction')->__('Shipping'),
                'index' => 'product_images',
                'renderer'  => 'amoaction/adminhtml_renderer_shipping' . ($export ? '_export' : ''),
                'filter' => false,
                'sortable'  => false,
            ), 'entity_id');
        }
    }
}
