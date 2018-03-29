<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 12/4/13
 * Time: 10:04 AM
 */
class Monkebusiness_Rollback_Helper_Product extends Mage_Core_Helper_Abstract
{

    public function getAttributeTypeByCode($attributeCode)
    {
        $type = Mage::getModel("eav/config")->getAttribute('catalog_product', $attributeCode)->getBackendType();
        return $type;
    }

    public function getAttributeIdByCode($attributeCode)
    {
        $id = Mage::getModel("eav/config")->getAttribute('catalog_product', $attributeCode)->getAttributeId();
        return $id;
    }


}