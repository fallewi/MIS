<?php

/**
 * Class Cminds_MultiUserAccounts_Model_Resource_Sales_Order_Collection
 */
class Cminds_MultiUserAccounts_Model_Resource_Sales_Order_Collection
    extends Mage_Sales_Model_Resource_Order_Collection
{

    public function getOrderAmountByDate($period, $subAccountId)
    {
        $this->addFieldToFilter('subaccount_id', array('eq' => $subAccountId));
        $this->filterByDate($period);

        $amount = $this->collectAmount();
        return $amount;
    }

    /**
     * @param $period
     * @return $this
     */
    public function filterByDate($period)
    {
        $now = Mage::getModel('core/date')->timestamp(time());

        switch ($period) {
            case 'Day':
                $this->addFieldToFilter('created_at', array('gt' => date("Y-m-d H:i:s", strtotime('-24 hours', $now))));
                break;
            case 'Month':
                $this->addFieldToFilter('created_at', array('gt' => date("Y-m-d H:i:s", strtotime('-1 month', $now))));
                break;
            case 'Year':
                $this->addFieldToFilter('created_at', array('gt' => date("Y-m-d H:i:s", strtotime('-1 year', $now))));
                break;
        }

        return $this;
    }

    public function collectAmount()
    {
        $amount = 0;
        foreach ($this as $order) {
            $amount += $order->getGrandTotal();
        }

        return $amount;
    }
}
