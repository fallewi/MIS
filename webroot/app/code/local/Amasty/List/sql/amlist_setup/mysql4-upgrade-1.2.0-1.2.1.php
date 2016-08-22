<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_List
 */
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amlist/list')}` ADD `is_default` SMALLINT NOT NULL; 
");

$this->endSetup();