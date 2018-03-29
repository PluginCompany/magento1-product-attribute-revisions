<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$changes = Mage::getModel('monkebusiness_rollback/change')->getCollection();

if ($changes->getSize() > 0) {
    foreach ($changes as $change) {
        $oldAdminUserId = $change->getAdminUserId();
        $change->setAdminUserId($oldAdminUserId . '_0')->save();
    }
}

$installer->endSetup();
