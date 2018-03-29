<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 12/3/13
 * Time: 9:09 PM
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$table = $installer->getTable('monkebusiness_rollback/change');
$sql= "
ALTER TABLE `$table`
  ADD COLUMN `store_id` int(10) NOT NULL;";

$installer->run($sql);

$installer->endSetup();