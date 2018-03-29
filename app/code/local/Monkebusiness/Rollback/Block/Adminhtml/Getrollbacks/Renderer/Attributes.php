<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 14/12/13
 * Time: 21:16
 */
class Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Attributes extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $values = $row->getChangeItems();
        if (!$values->count()) {
            return 'No changes made';
        }
        $return = '';
        $i = 1;
        foreach ($values as $v) {
            $v = $v->getAttributeCode();
            if(is_numeric($v)){
                $v = number_format($v, 2,'.','');
                if(floor($v) == $v){
                    $v = number_format($v, 0,'','');
                }
            }
            elseif(strlen((string)$v > 50)){
                $v = substr($v, 0, 50) . '...';
            }

            if ($i % 2 == 0) {
                $class = 'even';
            }else{
                $class = 'odd';
            }

            $return .= '<div class="' . $class . '">' . $v . '</div>';
            $i++;
        }
        return $return;
    }
}