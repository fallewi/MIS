<?php
/**
 * @package     BlueAcorn\CacheManagementMods
 * @version     
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */
class BlueAcorn_CacheManagementMods_Adminhtml_CachemodController extends Mage_Adminhtml_Controller_Action {

    public function getHelper()
    {
        return Mage::helper('blueacorn_cachemanagementmods');
    }

    public function indexAction()
    {
        if($this->getHelper()->isEnabled())
        {
            $action = $this->getAction();
            $list = $this->getList();
            foreach($list as $item)
            {
                $trigger = $this->getHelper()->sanitizeLabel($item['label']);
                if($trigger == $action)
                {
                    try {
                        $torun = 'php '.Mage::getBaseDir().DS.'shell'.DS.$item['command'].'.php '.$item['params'];
                        if(!$this->passCheck($torun)) throw new Exception('Will not run this command because it seems too dangerous: '.$torun);
                        exec('php '.Mage::getBaseDir().DS.'shell'.DS.$item['command'].'.php '.$item['params'], $output);
                        $output = implode("\n", $output);
                        $this->_getSession()->addSuccess($this->__( 'Script attempted: ') . $item['command']. ' with '. $item['params'] .'<br><pre style="padding: 10px; border: 1px solid grey">'.$output.'</pre>');
                    } catch(Exception $e) {
                        $this->_getSession()->addEror($this->__( 'Script failed, please try again or contact administrator: ') . $item['command']);
                    }
                }
            }
        }
        $this->_redirect( '*/cache' );
    }

    public function getAction()
    {
        return Mage::app()->getRequest()->getParam('action');
    }

    public function getList()
    {
        return $this->getHelper()->getButtonList();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('system/cache');
    }

    protected function passCheck($cmd)
    {
        if(strpos($cmd, ' rm ')) return false;
        if(strpos($cmd, ';')) return false;
        return true;
    }

}
