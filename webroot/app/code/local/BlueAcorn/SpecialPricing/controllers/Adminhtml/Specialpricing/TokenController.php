<?php
/**
 * BlueAcorn_SpecialPricing extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       BlueAcorn
 * @package        BlueAcorn_SpecialPricing
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Token admin controller
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_SpecialPricing
 * @author      Ultimate Module Creator
 */
class BlueAcorn_SpecialPricing_Adminhtml_Specialpricing_TokenController extends BlueAcorn_SpecialPricing_Controller_Adminhtml_SpecialPricing
{
    /**
     * init the token
     *
     * @access protected
     * @return BlueAcorn_SpecialPricing_Model_Token
     */
    protected function _initToken()
    {
        $tokenId  = (int) $this->getRequest()->getParam('id');
        $token    = Mage::getModel('blueacorn_specialpricing/token');
        if ($tokenId) {
            $token->load($tokenId);
        }
        Mage::register('current_token', $token);
        return $token;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('blueacorn_specialpricing')->__('MAP Pricing'))
             ->_title(Mage::helper('blueacorn_specialpricing')->__('Tokens'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit token - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $tokenId    = $this->getRequest()->getParam('id');
        $token      = $this->_initToken();
        if ($tokenId && !$token->getId()) {
            $this->_getSession()->addError(
                Mage::helper('blueacorn_specialpricing')->__('This token no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getTokenData(true);
        if (!empty($data)) {
            $token->setData($data);
        }
        Mage::register('token_data', $token);
        $this->loadLayout();
        $this->_title(Mage::helper('blueacorn_specialpricing')->__('MAP Pricing'))
             ->_title(Mage::helper('blueacorn_specialpricing')->__('Tokens'));
        if ($token->getId()) {
            $this->_title($token->getToken());
        } else {
            $this->_title(Mage::helper('blueacorn_specialpricing')->__('Add token'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new token action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save token - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('token')) {
            try {
                $token = $this->_initToken();
                $token->addData($data);
                $token->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blueacorn_specialpricing')->__('Token was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $token->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setTokenData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blueacorn_specialpricing')->__('There was a problem saving the token.')
                );
                Mage::getSingleton('adminhtml/session')->setTokenData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('blueacorn_specialpricing')->__('Unable to find token to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete token - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $token = Mage::getModel('blueacorn_specialpricing/token');
                $token->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blueacorn_specialpricing')->__('Token was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blueacorn_specialpricing')->__('There was an error deleting token.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('blueacorn_specialpricing')->__('Could not find token to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete token - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $tokenIds = $this->getRequest()->getParam('token');
        if (!is_array($tokenIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('blueacorn_specialpricing')->__('Please select tokens to delete.')
            );
        } else {
            try {
                foreach ($tokenIds as $tokenId) {
                    $token = Mage::getModel('blueacorn_specialpricing/token');
                    $token->setId($tokenId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blueacorn_specialpricing')->__('Total of %d tokens were successfully deleted.', count($tokenIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blueacorn_specialpricing')->__('There was an error deleting tokens.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massStatusAction()
    {
        $tokenIds = $this->getRequest()->getParam('token');
        if (!is_array($tokenIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('blueacorn_specialpricing')->__('Please select tokens.')
            );
        } else {
            try {
                foreach ($tokenIds as $tokenId) {
                $token = Mage::getSingleton('blueacorn_specialpricing/token')->load($tokenId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d tokens were successfully updated.', count($tokenIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blueacorn_specialpricing')->__('There was an error updating tokens.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportCsvAction()
    {
        $fileName   = 'token.csv';
        $content    = $this->getLayout()->createBlock('blueacorn_specialpricing/adminhtml_token_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction()
    {
        $fileName   = 'token.xls';
        $content    = $this->getLayout()->createBlock('blueacorn_specialpricing/adminhtml_token_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction()
    {
        $fileName   = 'token.xml';
        $content    = $this->getLayout()->createBlock('blueacorn_specialpricing/adminhtml_token_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('blueacorn_specialpricing/token');
    }
}
