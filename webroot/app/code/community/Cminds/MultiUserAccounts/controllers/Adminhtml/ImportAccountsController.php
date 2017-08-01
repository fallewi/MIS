<?php

/**
 * @author CreativeMindsSolutions
 */
class Cminds_MultiUserAccounts_Adminhtml_ImportAccountsController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/customer');
    }

    private $_success_count = 0;
    private $_errors = array();

    public function indexAction() {

        $this->_title($this->__('Import accounts'));
        $this->loadLayout();
        $this->_setActiveMenu('customer');
        $this->renderLayout();
    }


    public function savePostAction() {

        if ($data = $this->getRequest()->getPost()) {

            if(isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
                try {

                    $uploader = new Varien_File_Uploader('file');
                    $uploader->setAllowedExtensions(array('csv'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);

                    $path = Mage::getBaseDir('var') . DS ;
                    $file = $uploader->save($path, $_FILES['file']['name'] );

                    $csvFile = file($path.$file['file']);
                    if(!$csvFile) {
                        Throw new Exception('File dosent exists.');
                    }

                    $data = array();
                    $i = 0;
                    foreach ($csvFile as $line) {
                        if($i != 0)
                            $data[] = str_getcsv($line,';');
                        else
                            $columns[] =  str_getcsv($line,';');
                        $i++;
                    }

                    $this->aasort($data,'5');

                    foreach($data as $row) {
                        if(empty($row[5]))
                            $this->addCustomer($row,$columns);
                        else
                            $this->addSubAccount($row,$columns);
                    }

                    @unlink($path.$file['file']);
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    $this->getResponse()->setRedirect($this->getUrl('*/*/index'));

                }

            }

            if(count($this->_errors) > 0) {
                Mage::getSingleton('adminhtml/session')->addError(implode('<br/>',$this->_errors));
            }

            if($this->_success_count > 0) {
                Mage::getSingleton('adminhtml/session')->addSuccess('Success: '.$this->_success_count);
            }

            $this->getResponse()->setRedirect($this->getUrl('*/*/index'));

        }
    }

    private function addCustomer($row,$columns) {

        $store = Mage::app()
            ->getWebsite(true)
            ->getDefaultGroup()
            ->getDefaultStore();

        $websiteId =  $store->getWebsiteId();

        try{


            $customer = Mage::getModel("customer/customer")->load($row[0]);

            if($customer->getId()) {
                $edit = 1;
            } else {
                $edit = 0;
                $customer = Mage::getModel("customer/customer");
            }

            $customer->setWebsiteId($websiteId)
                ->setStore($store)
                ->setFirstname($row[1])
                ->setLastname($row[2])
                ->setEmail($row[3]);

            if($edit == 0) {
                $customer->setPassword($row[4]);
            }
            if(count($columns[0])>12) {
                $i = 0;
                foreach($columns[0] as $column) {
                    if($i>=13 and isset($row[$i])) {

                        $customer->setData($column,$row[$i]);
                    }
                    $i++;
                }
            }

            $customer->save();
            $this->_success_count++;
        }
        catch (Exception $e) {
            $this->_errors[] = $e->getMessage().' User '.$row[3];

            Mage::log($e->getMessage().' - '.$row[3],null,'cminds_multiuser_import.log');

        }
    }

    private function addSubAccount($row) {


        $store = Mage::app()
            ->getWebsite(true)
            ->getDefaultGroup()
            ->getDefaultStore();

        $websiteId =  $store->getWebsiteId();

        $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->load($row[0]);
        if($subAccount->getId()) {
            $edit = 1;
            $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->setId($subAccount->getId());
        } else {
            $edit = 0;
            $subAccount = Mage::getModel('cminds_multiuseraccounts/subAccount')->setId(null);
        }


        try {
            $customer = Mage::getModel("customer/customer")->load($row[5]);

            if($customer->getId()) {
                $data['firstname'] = $row[1];
                $data['website_id'] = $websiteId;
                $data['lastname'] = $row[2];
                $data['email'] = $row[3];
                $data['parent_customer_id'] = $customer->getId();
                $data['store_id'] = $store->getId();
                if(isset($row[6]))
                    $data['permission'] = $row[6];
                if(isset($row[7]))
                    $data['view_all_orders'] = $row[7];
                if(isset($row[8]))
                    $data['can_see_cart'] = $row[8];
                if(isset($row[8]))
                    $data['can_see_cart'] = $row[8];
                if(isset($row[9]))
                    $data['have_access_checkout'] = $row[9];
                if(isset($row[10]))
                    $data['get_order_email'] = $row[10];
                if(isset($row[11]))
                    $data['get_order_invoice'] = $row[11];
                if(isset($row[12]))
                    $data['get_order_shipment'] = $row[12];

                $subAccount->addData($data);

                if ($errors = $subAccount->validate()) {

                    if($edit == 0) {
                      $subAccount->setPassword($row[4]);
                    }

                    $subAccount->save();
                    $this->_success_count++;
                    return;
                } else {
                    Mage::log($errors,null,'cminds_multiuser_import.log');
                }
            } else {
                Throw new Mage_Core_Exception('Customer dosent exists');
            }

        } catch (Mage_Core_Exception $e) {
            $this->_errors[] = $e->getMessage().' User '.$row[3];

            Mage::log($e->getMessage().' - '.$row[2],null,'cminds_multiuser_import.log');

        }
    }

    private function aasort (&$array, $key) {
        $sorter=array();
        $ret=array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii]=$va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii]=$array[$ii];
        }
        $array=$ret;
    }
}