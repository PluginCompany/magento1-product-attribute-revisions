<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 14/12/13
 * Time: 21:16
 */
class Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Attributename extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());

        $stockItem = $row->getData('is_stockitem');
        if($stockItem){
            return $row->getData('attribute_code');
        }

        $attr = Mage::getModel('eav/entity_attribute')->load($value);

        $label = $attr->getStoreLabel();
        return $label;
    }
}