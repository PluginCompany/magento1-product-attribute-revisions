<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 14/12/13
 * Time: 21:16
 */
class Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Difflink extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $stockItem = $row->getData('is_stockitem');
        if($stockItem){
            return 'Stock Attribute';
        }

        $value = $row->getData($this->getColumn()->getIndex());

        $attrId = $row->getData('attribute_id');
        $attr = Mage::getModel('eav/entity_attribute')->load($attrId);
        $input = $attr->getFrontendInput();

        if (stristr($input, 'textarea')) {
            return '<span style="color:#ea7601;text-decoration:underline;cursor:pointer;" onclick="showDifference(\'' . Mage::helper('adminhtml')->getUrl('adminhtml/product/differenceview',array('id'=>$value)) . '\')">Compare</span>';
        }
    }
}
