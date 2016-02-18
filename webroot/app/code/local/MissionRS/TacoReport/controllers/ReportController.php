<?php
/**
 * Class MissionRS_TacoReport_ReportController
 * @author Victor Cortez <victorc@missionrs.com>
 * @version 1.0
 * @package MissionRS_TacoReport
 */
class MissionRS_TacoReport_ReportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Taco Report'));
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Controller for the single date form
     */
    public function dateAction()
    {
        $data = array();
        $params = $this->getRequest()->getParams();

        if($params['date-input-box'])
        {
            $date = date('Y-m-d',strtotime($params['date-input-box']));
            $data['block_date'] = $date;
            $this->_redirect('*/*/index',$data);
        }
    }

    /**
     * Controller for the Date Range form
     */
    public function customRangeAction()
    {
        $params = $this->getRequest()->getParams();

        if($params['date-input-box-from'] && $params['date-input-box-to'] )
        {
            $dateTo = date('Y-m-d',strtotime($params['date-input-box-to']));
            $dateFrom = date('Y-m-d',strtotime($params['date-input-box-from']));
            $data['block_date_to'] = $dateTo;
            $data['block_date_from'] = $dateFrom;
            $this->_redirect('*/*/index',$data);
        }
    }

    /**
     * This was added for compliance with security patch SUPEE 6285
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('missionrs_tacoreport');
    }
}