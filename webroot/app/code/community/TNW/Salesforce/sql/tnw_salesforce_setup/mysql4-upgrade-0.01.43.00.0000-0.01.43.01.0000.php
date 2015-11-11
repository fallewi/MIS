<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->gettable('tnw_salesforce_queue_storage')};
CREATE TABLE {$this->gettable('tnw_salesforce_queue_storage')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL,
  `mage_object_type` varchar(255) NOT NULL COMMENT 'set varchar type as we dont know which types well store in column',
  `sf_object_type` varchar(255) NOT NULL COMMENT 'set varchar type as we dont know which types well store in column',
  `date_created` datetime NOT NULL,
  `status` enum('new','sync_running') NOT NULL DEFAULT 'new',
  `sync_attempt` int(11) NOT NULL DEFAULT '0' COMMENT 'sync attempt number',
  `date_sync` datetime DEFAULT NULL,
  `message` text COMMENT 'serialized error array ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `object_id_sf_object_type` (`object_id`,`sf_object_type`),
  KEY `mage_object_type` (`mage_object_type`),
  KEY `date_created` (`date_created`),
  KEY `status` (`status`),
  KEY `sync_attempt` (`sync_attempt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();