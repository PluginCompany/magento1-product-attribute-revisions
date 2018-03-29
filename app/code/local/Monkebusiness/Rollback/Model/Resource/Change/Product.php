<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 12/3/13
 * Time: 9:10 PM
 */ 
class Monkebusiness_Rollback_Model_Resource_Change_Product extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('monkebusiness_rollback/change_product', 'change_product_id');
    }
}

