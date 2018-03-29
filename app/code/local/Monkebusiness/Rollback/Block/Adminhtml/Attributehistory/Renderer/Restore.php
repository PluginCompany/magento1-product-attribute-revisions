<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 14/12/13
 * Time: 21:16
 */
class Monkebusiness_Rollback_Block_Adminhtml_Attributehistory_Renderer_Restore extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $changeId = $row->getData('change_product_id');
        $prntId = $row->getData('change_parent_id');
        $return = '<a style="cursor:pointer" href="'
            . Mage::helper('adminhtml')->getUrl('adminhtml/product/rollback',array('change_ids'=>$changeId,'change_id'=>$prntId,'is_popover'=>true))
            . '">Restore</a>';
        return $return;
    }
}