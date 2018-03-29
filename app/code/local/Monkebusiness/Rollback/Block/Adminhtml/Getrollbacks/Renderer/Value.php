<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 14/12/13
 * Time: 21:16
 */
class Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Value extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $values = $row->getChangeItems();
        if (!$values->count()) {
            return 'No changes made';
        }

        $index = $this->getColumn()->getIndex();

        $return = '';
        $i = 1;
        foreach ($values as $item) {
            $value = $item->getData($index);

            if($item->getIsStockitem()){
                $value = Mage::helper('monkebusiness_rollback')->getStockAttrOption($item->getData('attribute_code'), $item->getData($index));
            }

            if ($selectValue = $this->getSelectValue($item->getAttributeId(),$item->getData($index))) {
                $value = $selectValue;
            }
            elseif(is_numeric($value)){
                $value = number_format($value, 2,'.','');
                if(floor($value) == $value){
                    $value = number_format($value, 0,'','');
                }
            }
            elseif(strlen($value) > 25){
                $value = strip_tags($value);
                $value = str_replace("\n", ' ', $value);
                $value = str_replace("\r", ' ', $value);
                $value = substr($value, 0, 25) . '...';
            }

            if ($i % 2 == 0) {
                $class = 'even';
            }else{
                $class = 'odd';
            }

            $return .= '<div class="' . $class . '">' . $value . '</div>';
            $i++;
        }

        return $return;
    }

    public function getSelectValue($id,$value)
    {
        $attr = Mage::getModel('eav/entity_attribute')->load($id);
        $input = $attr->getFrontendInput();
        if (stristr($input, 'select')) {
            if ( ! $attr->getSourceModel()) {
                $attr->setSourceModel('eav/entity_attribute_source_table');
            }
            $value = explode(',',$value);
            foreach($value as $k=>$val){
                $v  = $attr->getSource()->getOptionText($val);
                if (is_array($v)) {
                    $v = $v['label'];
                }
                $value[$k] = $v;
            }
            $value = implode(', ',$value);
            return $value;
        }
        return false;

    }
}