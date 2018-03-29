<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 12/3/13
 * Time: 9:09 PM
 */ 
class Monkebusiness_Rollback_Model_Resource_Change_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('monkebusiness_rollback/change');
    }

    protected function _afterLoad(){
        foreach($this->getItems() as $item){
            $changeItems = Mage::getModel('monkebusiness_rollback/change_product')
                ->getCollection()
                ->addFieldToFilter('change_parent_id', $item->getId())
                ->setOrder('attribute_code');
            $item->setChangeItems($changeItems);
        }
    }

}