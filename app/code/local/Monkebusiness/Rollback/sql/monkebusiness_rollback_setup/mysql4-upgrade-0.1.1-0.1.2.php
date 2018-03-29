<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->modifyColumn(
    $installer->getTable('monkebusiness_rollback/change'),
    'admin_user_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'default'   => '',
        'comment'   => 'Admin User Id'
    )
)
;

$installer->endSetup();
