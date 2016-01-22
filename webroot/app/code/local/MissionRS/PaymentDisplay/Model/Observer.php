<?php
/**
 * victorc@missionrs.com 01/21/2016
 * Accept 1 payment method on frontend, and more payment options on the backend (for phone orders and in store pickup)
 */
class MissionRS_PaymentDisplay_Model_Observer
{
    public function checkPaymentOptions ( $event )
    {
        if ( $event->getResult ()->isAvailable )
        {
            //Any payment code added to this array will be disabled on the frontend.
            $frontEndNotAvailable = array ('checkmo');
            if ( in_array ( $event->getMethodInstance ()->getCode () , $frontEndNotAvailable ) )
            {
                $result = $event->getResult ();
                $result->isAvailable = false;
            }
        }
    }
}