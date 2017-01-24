<?php

/**
 * @package BlueAcorn_Productpage
 * @version 1.0.0
 * @author Ryan Corn
 * @copyright Copyright (c) 2015 Blue Acorn, Inc.
 */

class BlueAcorn_CsvExport_Model_System_Config_Source_Emailaddresses extends BlueAcorn_MiniGrid_Model_System_Config_Source_Minigrid_Abstract {

    /**
     * @return array
     */
    protected function _getFields() {
        return array(
            "email_name" => array("width" => "300px", "type" => "text"),
            "email_address" => array("width" => "300px", "type" => "text"),
        );
    }
}