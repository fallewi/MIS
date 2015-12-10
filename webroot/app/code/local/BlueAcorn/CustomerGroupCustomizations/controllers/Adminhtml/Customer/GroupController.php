<?php
/**
 * @package     BlueAcorn\CustomerGroupCustomizations
 * @version     0.1.0
 * @author      Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright   Copyright © 2015 Blue Acorn, Inc.
 */
require_once 'Levementum/GroupDisplay/controllers/Adminhtml/Customer/GroupController.php';


class BlueAcorn_CustomerGroupCustomizations_Adminhtml_Customer_GroupController extends Levementum_GroupDisplay_Adminhtml_Customer_GroupController {

    public function saveAction() {
        $customerGroup = Mage::getModel('customer/group');
        $id = $this->getRequest()->getParam('id');
        if (!is_null($id)) {
            $customerGroup->load((int)$id);
        }
        $linkedCategoryId = $this->getRequest()->getParam('linked_category');
        if($linkedCategoryId) {
            $customerGroup->setLinkedCategory($linkedCategoryId);
        }
        else {
            $customerGroup->setLinkedCategory(Mage::helper('blueacorn_customergroupcustomizations').NO_CATEGORY_SELECTED);
        }

        $customerGroup->save();
        parent::saveAction();
    }
}