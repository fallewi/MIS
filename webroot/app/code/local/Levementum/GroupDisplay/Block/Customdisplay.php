<?php

class Levementum_GroupDisplay_Block_Customdisplay extends Mage_Core_Block_Template{
    public function _construct(){
        parent::_construct();
        $this->setTemplate('levementum/groupdisplay/customdisplay.phtml');
    }
}