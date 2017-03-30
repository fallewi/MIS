<?php
 
$this->startSetup();

$this->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('amlistl/shared')} (
      `item_id` int(10) unsigned NOT NULL auto_increment,
      `list_id` int(10) unsigned NOT NULL,
      `customer_id` int(10) unsigned NOT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY  (`item_id`),
      KEY `IDX_CUSTOMER_SHARED` (`customer_id`),
      CONSTRAINT `FK_AMLISTL_LIST_LIST` FOREIGN KEY (`list_id`) REFERENCES `{$this->getTable('amlist/list')}` (`list_id`) ON DELETE CASCADE,
      CONSTRAINT `FK_AMLISTL_LIST_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `{$this->getTable('customer_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;  
");

$this->endSetup();
