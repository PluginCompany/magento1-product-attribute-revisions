<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 14/12/13
 * Time: 21:16
 */
class Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Popupvalue extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());

        $attrId = $row->getData('attribute_id');
        $attr = Mage::getModel('eav/entity_attribute')->load($attrId);
        $input = $attr->getFrontendInput();

        if (stristr($input, 'select')) {
            if ( ! $attr->getSourceModel()) {
                $attr->setSourceModel('eav/entity_attribute_source_table');
            }
            $value = $attr->getSource()->getOptionText($value);
        }elseif(is_numeric($value)){
            $value = number_format($value, 2,'.','');
            if(floor($value) == $value){
                $value = number_format($value, 0,'','');
            }
        }

        if($row->getIsStockitem()){
            return Mage::helper('monkebusiness_rollback')->getStockAttrOption($row->getAttributeCode(),$value);
        }
        return nl2br(htmlspecialchars($value));
    }
}
