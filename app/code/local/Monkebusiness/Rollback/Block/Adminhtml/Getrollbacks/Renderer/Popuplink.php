<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 14/12/13
 * Time: 21:16
 */
class Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Popuplink extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $return = '<a style="cursor:pointer" onclick="openRollbackPopup(\'' . Mage::helper('adminhtml')->getUrl('adminhtml/product/popup',array('change_id'=>$value)) . '\')">View</a>';
        return $return;
    }
}