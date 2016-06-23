<?php

/**
 * Class MissionRS_TacoReport_Block_Report
 * @author Victor Cortez <victorc@missionrs.com>
 * @version 1.0
 * @package MissionRS_TacoReport
 */
class MissionRS_TacoReport_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Template
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('missionrs/tacoreport.phtml');
    }

    /**
     * @param string $first beginning of timeframe to retrieve order data for
     * @param string $last end of timeframe to retrieve order data for
     * @return array returns array of formated dates to start and end for order collection
     */
    public function setFirstLastDate($first,$last)
    {
        $fromDate = gmdate('Y-m-d H:i:s', strtotime($first));
        $toDate = gmdate('Y-m-d H:i:s',  strtotime($last . " +1 day"));

        return array("from_date"=>$fromDate,"to_date"=>$toDate);
    }

    /**
     * Function to return collection of orders for a specific date
     * @param $date
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function getOrdersCollection($date)
    {
        $dates = $this->setFirstLastDate($date,$date);
        $currentDate = $dates["from_date"];
        $nextDay = $dates["to_date"];

        $customerGroupsToFilter = explode(',',Mage::getStoreConfig('missionrs_tacoreport/tacoreport_configuration/tacoreport_configuration_customer_group_filter'));
        //print_r(array_values($customerGroupsToFilter));
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('created_at', array('from' => $currentDate, 'to' => $nextDay))
            ->addAttributeToFilter(
                'status' , array('nin' =>
                    array(
                        Mage_Sales_Model_Order::STATE_CANCELED
                    ))
            )
            ->addAttributeToFilter('customer_group_id', array('nin' => $customerGroupsToFilter))
            ->addAttributeToSelect('*');

        return $orders;
    }

    /**
     * This function will return a collection of orders from the beginning of the month of and to the selected date passed
     * @param $date
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function getCollectionOfCurrentMonth($date)
    {
        $dates = $this->setFirstLastDate(date('Y-m-01 H:i:s',strtotime($date)),$date);
        $firstDateOfMonth = $dates["from_date"];
        $lastDate = $dates["to_date"];

        $customerGroupsToFilter = explode(',',Mage::getStoreConfig('missionrs_tacoreport/tacoreport_configuration/tacoreport_configuration_customer_group_filter'));

        $monthCollection = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('created_at', array('from' => $firstDateOfMonth, 'to' => $lastDate))
            ->addAttributeToFilter(
                'status' , array('nin' =>
                    array(
                        Mage_Sales_Model_Order::STATE_CANCELED
                    ))
            )
            ->addAttributeToFilter('customer_group_id', array('nin' => $customerGroupsToFilter))
            ->addAttributeToSelect('*');
        //echo count($monthCollection).'<br/>';

        return $monthCollection;
    }

    /**
     * This function will retrieve the collection of orders of the same month as the date selected for the previous year.
     * @param $date
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function getCollectionOfPreviousYear($date)
    {
        $dates = $this->setFirstLastDate(date('Y-m-01 H:i:s', strtotime($date . " -1 year")),date('Y-m-t H:i:s', strtotime($date . " -1 year")));
        $dateFromMinusYear = $dates["from_date"];
        $lastDayOfMonth = $dates["to_date"];

        $customerGroupsToFilter = explode(',',Mage::getStoreConfig('missionrs_tacoreport/tacoreport_configuration/tacoreport_configuration_customer_group_filter'));

        $monthCollection = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('created_at', array('from' => $dateFromMinusYear, 'to' => $lastDayOfMonth))
            ->addAttributeToFilter
            (
                'status' , array('nin' =>
                    array(
                        Mage_Sales_Model_Order::STATE_CANCELED
                    ))
            )
            ->addAttributeToFilter('customer_group_id', array('nin' => $customerGroupsToFilter))
            ->addAttributeToSelect('*');
        return $monthCollection;
    }

    /**
     * This function will retrieve a collection of orders for a specific time frame
     * @param $fromDate
     * @param $toDate
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function getOrdersCollectionByDate($fromDate, $toDate)
    {
        $dates = $this->setFirstLastDate($fromDate,$toDate);
        $fromDate = $dates["from_date"];
        $toDate = $dates["to_date"];

        $customerGroupsToFilter = explode(',',Mage::getStoreConfig('missionrs_tacoreport/tacoreport_configuration/tacoreport_configuration_customer_group_filter'));

        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))
            ->addAttributeToFilter(
                'status' , array('nin' =>
                    array(
                        Mage_Sales_Model_Order::STATE_CANCELED
                    ))
            )
            ->addAttributeToFilter('customer_group_id', array('nin' => $customerGroupsToFilter))
            ->addAttributeToSelect('*');
        //echo count($orders).'<br/>';

        return $orders;
    }

    /**
     * This Function will retrieve the sales totals for Admins from a collection of orders
     * @param $collection
     * @return array
     */
    public function getAssistedSalesTotals($collection)
    {
        $assitedSalesTotals = array();

        foreach ($collection as $order)
        {
            //force id to 0 for non assisted sales
            if(!$order->getAdminId())
            {
                $order->setAdminId(0);
            }
            //add
            if(array_key_exists($order->getAdminId(),$assitedSalesTotals))
            {
                $assitedSalesTotals[$order->getAdminId()] = $assitedSalesTotals[$order->getAdminId()] + $order->getGrandTotal() ;
            }
            else
            {
                //first entry
                $assitedSalesTotals[$order->getAdminId()] = $order->getGrandTotal();
            }
        }
        return $assitedSalesTotals;
    }

    /**
     * This function is used to retrieve the total sales from a specific customer (Greenavise)
     * @param $collection
     * @return int
     */
    public function getGreenaviseDailyTotal($collection)
    {
        $totalGreenaviseSales = 0;
        foreach($collection as $item)
        {
            if($item->getBillingAddress()->getCompany() == "Greenavise")
            {
                $totalGreenaviseSales += $item->getGrandTotal();
            }
            else
            {
                continue;
            }
        }
        return $totalGreenaviseSales;
    }

    public function getSalespersonCollection()
    {
        $collection = Mage::getResourceModel('admin/user_collection');
        $collection->join(array('role' => 'admin/role'),'main_table.user_id=role.user_id');
        $collection->join(array('role2' => 'admin/role'),'role.parent_id=role2.role_id',array('role_group' => 'role_name', 'role_group_id' => 'role_id'));

        $options = array();
        foreach ($collection as $item) {
            $options[$item->getUsername()] = $item;
        }

        return $options;
    }

    public function getDateToProcess()
    {
        if(!$this->getBlockDate())
        {
            $this->setBlockDate(date('Y-m-d', time()));
        }
        return $this->getBlockDate();
    }

    public function getBrandsCollection($collection)
    {
        // Collection of orders
        $dateCollectionArray = $collection->getColumnValues('entity_id');
        // Gets Collection of Items from Orders
        $orderItemCollection = Mage::getResourceModel('sales/order_item_collection')->addAttributeToFilter('order_id', array('in' => $dateCollectionArray))->addAttributeToSelect('sku')->addAttributeToSelect('qty_ordered');
        // Gets Array of SKUs
        $itemSkuArray = $orderItemCollection->getColumnValues('sku');
        $itemQtyArray = $orderItemCollection->getColumnValues('qty_ordered');

        $i = 0;
        $skuQtyArray = array();
        foreach($itemSkuArray as $itemSku)
        {
            if(!isset($skuQtyArray[$itemSku]))
            {
                $skuQtyArray[$itemSku] = (int)$itemQtyArray[$i];
            }
            else
            {
                $skuQtyArray[$itemSku] = $skuQtyArray[$itemSku] + (int)$itemQtyArray[$i];
            }
            $i++;
        }

        $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('sku', array('in' => $itemSkuArray))->addAttributeToSelect('manufacturer');

        $brands = array();
        foreach($productCollection as $item)
        {
            if(!isset($brands[$item->getAttributeText('manufacturer')]))
            {
                $brands[$item->getAttributeText('manufacturer')] = $skuQtyArray[$item->getSku()];
            }
            else
            {
                $brands[$item->getAttributeText('manufacturer')] = $brands[$item->getAttributeText('manufacturer')] + $skuQtyArray[$item->getSku()];
            }
        }

        return $brands;
    }

    public function getProductsCollection($collection)
    {
        $dateCollectionArray = $collection->getColumnValues('entity_id');
        $AllProducts = array();
        if(count($dateCollectionArray > 0))
        {
            foreach($dateCollectionArray as $entity)
            {
                $products = Mage::getModel('sales/order')->load($entity)->getAllItems();
                foreach($products as $product)
                {
                    array_push($AllProducts, array($product->getName() => $product->getQtyOrdered()));
                }
            }
            return $AllProducts;
        }
        else
        {
            return null;
        }
    }

    public function getDailyReportUrl()
    {
        return $this->getUrl('*/*/date');
    }

    public function getCustomDateReportUrl()
    {
        return $this->getUrl('*/*/customRange');
    }
}