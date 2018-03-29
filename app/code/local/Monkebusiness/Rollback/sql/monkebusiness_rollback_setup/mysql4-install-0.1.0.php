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
$pTable = $installer->getTable('monkebusiness_rollback/change_product');
$sql= "
DROP TABLE IF EXISTS `$table`;

CREATE TABLE `$table` (
  `change_id` int(10) NOT NULL AUTO_INCREMENT,
  `change_type_id` int(1) NOT NULL,
  `admin_user_id` int(10) NOT NULL,
  `changed_entity_id` int(10) NOT NULL,
  `website_id` int(3) DEFAULT '0',
  `change_date` datetime DEFAULT NULL,
  `is_rollback` int(1) DEFAULT '0',
  `original_change_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`change_id`)
);

DROP TABLE IF EXISTS `$pTable`;

CREATE TABLE `$pTable` (
  `change_product_id` int(10) NOT NULL AUTO_INCREMENT,
  `change_parent_id` int(10) DEFAULT NULL,
  `product_id` int(10) DEFAULT NULL,
  `attribute_id` int(10) NOT NULL,
  `attribute_code` varchar(255) NOT NULL,
  `is_stockitem` int(10) DEFAULT NULL,
  `original_value` text,
  `new_value` text,
  `is_rolled_back` int(1) DEFAULT NULL,
  `rollback_change_item_id` int(10) DEFAULT NULL,
  `rollback_user_id` int(10) DEFAULT NULL,
  `rollback_parent_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`change_product_id`)
);
";

$installer->run($sql);

$installer->endSetup();