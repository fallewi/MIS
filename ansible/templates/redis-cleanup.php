<?php PHP_SAPI == 'cli' or die('<h1>:P</h1>');

ini_set('memory_limit','1024M');
set_time_limit(0);
error_reporting(E_ALL | E_STRICT);

// CHANGE THIS BELOW LINE TO MATCH DEPLOYMENT
/////////////////////////////////////////////
require_once '{{ MAGE_ROOT }}/app/Mage.php';

Mage::app()->getCache()->getBackend()->clean('old');
Enterprise_PageCache_Model_Cache::getCacheInstance()->getFrontend()->getBackend()->clean('old');
