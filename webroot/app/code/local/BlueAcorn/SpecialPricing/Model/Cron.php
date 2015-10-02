<?php

class BlueAcorn_SpecialPricing_Model_Cron {

    public function checkExpiredTokens()
    {
        $tokens = Mage::getModel('blueacorn_specialpricing/token')->getCollection();

        foreach($tokens as $token)
        {
            if($token->getTokenExpirationDate() < time())
            {
                $token->delete();
            }
        }
    }
}