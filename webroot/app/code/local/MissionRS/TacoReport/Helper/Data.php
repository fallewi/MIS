<?php
/**
 * @category    MissionRS
 * @author      Victor Cortez <victorc@missionrs.com>
 * @version     1.0
 * @package     MissionRS_TacoReport
 * @date        09/21/15 9:00 AM
 * @brief       
 * @details     
 */ 
class MissionRS_TacoReport_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCurrentDate()
    {
        $currentTimestamp = time();
        $date = date('F d, Y', $currentTimestamp);
        return $date;
    }
/*
    public function getFirstDayOfYear()
    {
        $firstDate = date('Y-m-d', strtotime("first day of january ".date('Y')));
        return $firstDate;
    }
*/
    public function getGrandTotal($orders = array())
    {
        $grandTotal = 0;
        foreach($orders as $order){
            $grandTotal += $order->getGrandTotal();
        }
        return $grandTotal;
    }

    /**
     * @param Mage_Sales_Model_Resource_Order_Collection $collection
     * @param $attributeToGroup
     *
     * @return mixed
     */
    public function groupCollection($collection, $attributeToGroup)
    {
        $collection->getSelect()->group('main_table.admin_id');
        return $collection;
    }

    public function getSalesPersonTotal($collection)
    {
        $totalSalespersonSales = 0;
        foreach($collection as $item)
        {
            if($item->getAdminId() == null)
            {
                continue;
            }
            else
            {
                $totalSalespersonSales += $item->getGrandTotal();
            }
        }
        return $totalSalespersonSales;
    }

    public function getNonSalesPersonTotal($collection)
    {
        $totalSalespersonSales = 0;
        foreach($collection as $item){
            if($item->getAdminId() == null)
            {
                $totalSalespersonSales += $item->getGrandTotal();
            }
            else
            {
                continue;
            }
        }
        return $totalSalespersonSales;
    }
}