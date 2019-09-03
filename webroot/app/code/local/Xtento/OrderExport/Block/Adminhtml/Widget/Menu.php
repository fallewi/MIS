<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            vPGjkQHqxXo20xCC7zQ8CGcLxhRkBY+cGe1+8TjDIvI=
 * Last Modified: 2019-05-07T22:19:58+02:00
 * File:          app/code/local/Xtento/OrderExport/Block/Adminhtml/Widget/Menu.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Block_Adminhtml_Widget_Menu extends Mage_Core_Block_Abstract
{
    protected $_menuBar;

    protected $_menu = array(
        'orderexport_manual' => array(
            'name' => 'Manual Export',
            'action_name' => '',
            'last_link' => false,
            'is_link' => true
        ),
        'orderexport_log' => array(
            'name' => 'Execution Log',
            'action_name' => '',
            'last_link' => false,
            'is_link' => true
        ),
        'orderexport_history' => array(
            'name' => 'Export History',
            'action_name' => '',
            'last_link' => false,
            'is_link' => true
        ),
        'orderexport_configuration' => array(
            'name' => 'Configuration',
            'last_link' => false,
            'is_link' => false,
        ),
        'orderexport_profile' => array(
            'name' => 'Export Profiles',
            'action_name' => '',
            'last_link' => false,
            'is_link' => true
        ),
        'orderexport_destination' => array(
            'name' => 'Export Destinations',
            'action_name' => '',
            'last_link' => false,
            'is_link' => true
        ),
        'orderexport_tools' => array(
            'name' => 'Tools',
            'action_name' => '',
            'last_link' => false,
            'is_link' => true
        ),
    );

    public function getMenu()
    {
        return $this->_menu;
    }

    protected function _toHtml()
    {
        $title = Mage::helper('xtento_orderexport')->__('Sales Export Navigation');
        $this->_menuBar = <<<EOT
        <style>
        .icon-head { padding-left: 0px; }
        </style>
        <script>
            function xtHasChanges() {
                var elements = $$(".changed");
                for (var i in elements) {
                    if (typeof elements[i].id !== "undefined" && elements[i].id !== "") {
                        return true;
                    }
                }
                return false;
            }
        </script>
        <div style="padding:8px; border: 1px solid #CDDDDD; background: #E7EFEF; font-size:12px;">
            <i>{$title}</i>&nbsp;-&nbsp;
EOT;
        foreach ($this->getMenu() as $controllerName => $entryConfig) {
            if ($entryConfig['is_link']) {
                if (!Mage::getSingleton('admin/session')->isAllowed('sales/orderexport/' . str_replace('orderexport_', '', $controllerName))) {
                    // No rights to see
                    continue;
                }
                $this->addMenuLink($entryConfig['name'], $controllerName, $entryConfig['action_name'], $entryConfig['last_link']);
            } else {
                $this->_menuBar .= '<i>' . $entryConfig['name'] . '</i>';
                if (!$entryConfig['last_link']) {
                    $this->_menuBar .= '&nbsp;|&nbsp;';
                }
            }
        }
        $this->_menuBar .= '<a id="page-help-link" href="https://support.xtento.com/wiki/Magento_Extensions:Magento_Order_Export_Module" target="_blank" style="color: #EA7601; text-decoration: underline; line-height: 16px;">' . Mage::helper('xtento_orderexport')->__('Help') . '</a>';
        $this->_menuBar .= '</div>';
        $utilsHelper = Mage::helper('xtcore/utils');
        if (method_exists($utilsHelper, 'getExtensionStatusString')) {
            // To avoid issues if someone didn't update XtCore for some reason
            $extensionStatus = $utilsHelper->getExtensionStatusString('Xtento_OrderExport', 'xtento_orderexport/system_config_backend_export_server');
            if (!empty($extensionStatus)) {
                $this->_menuBar .= '<div style="padding:8px; margin-bottom: 10px; border: 1px solid #CDDDDD; background: #E7EFEF; border-top: 0; font-size:12px;">';
                $this->_menuBar .= '<div style="float:right;"><a href="https://www.xtento.com/" target="_blank" style="text-decoration:none;color:#57585B;"><img src="//www.xtento.com/media/images/extension_logo.png" alt="XTENTO" height="20" style="vertical-align:middle;"/> XTENTO Magento Extensions</a></div>';
                $this->_menuBar .= $extensionStatus;
                $this->_menuBar .= '</div>';
            } else {
                $this->_menuBar .= "<br/><!--Could not retrieve extension status-->";
            }
        } else {
            $this->_menuBar .= "<br/>";
        }

        return $this->_menuBar;
    }

    private function addMenuLink($name, $controllerName, $actionName = '', $lastLink = false)
    {
        $isActive = '';
        if ($this->getRequest()->getControllerName() == $controllerName) {
            if ($actionName == '' || $this->getRequest()->getActionName() == $actionName) {
                $isActive = 'font-weight: bold;';
            }
        }
        $showWarning = '';
        if ($this->getShowWarning()) {
            $showWarning = "if (xtHasChanges()) { if (!confirm('" . Mage::helper('xtento_orderexport')->__('You have unsaved changes. Click OK to leave without saving your changes.') . "')) { return false; } }";
        }
        $this->_menuBar .= '<a href="' . Mage::helper('adminhtml')->getUrl('*/' . $controllerName . '/' . $actionName) . '" onclick="' . $showWarning . '" style="' . $isActive . '">' . Mage::helper('xtento_orderexport')->__($name) . '</a>';
        if (!$lastLink) {
            $this->_menuBar .= '&nbsp;|&nbsp;';
        }
    }

    public function isEnabled()
    {
        return Xtento_OrderExport_Model_System_Config_Source_Order_Status::isEnabled();
    }
}