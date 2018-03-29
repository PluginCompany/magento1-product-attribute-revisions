<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Attribute Revisions, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Monkebusiness_Rollback_Block_Adminhtml_Textoption extends Mage_Core_Block_Template
{
    protected $_change;

    public function getAttributeArray()
    {
        $textIds = $this->getRequest()->getParam('text_ids');
        if (!is_array($textIds)) {
            $textIds = explode(',', $textIds);
        }

        $textArray = array();
        foreach ($textIds as $id) {
            $changeItem = $this->getChangeItem($id);
            $attr = Mage::getModel('eav/entity_attribute')->load($changeItem->getAttributeId());
            $label = $attr->getStoreLabel();

            $currentValue = Mage::getResourceModel('catalog/product')
                ->getAttributeRawValue($changeItem->getProductId(), $changeItem->getAttributeCode(), $changeItem->getChange()->getStoreId());

            $textArray[] = array(
                'id' => $id,
                'label' => $label,
                'current_value' => $currentValue,
                'old_value' => $changeItem->getOriginalValue(),
                'new_value' => $changeItem->getNewValue(),
                'value_to_save' => ''
            );
        }
        return $textArray;
    }

    public function getTextJSON()
    {
        return json_encode($this->getAttributeArray());
    }

    public function getNumericJSON()
    {
        if ($nm = $this->getRequest()->getParam('numeric')) {
            $numeric = array();
            foreach ($nm as $k => $v) {
                $numeric[] = array(
                    'id' => $k,
                    'value_to_save' => $v
                );
            }
            return json_encode($numeric);
        }
        return 'false';
    }

    public function getChangeItem($id)
    {
        $change = Mage::getModel('monkebusiness_rollback/change_product')->load($id);
        return $change;
    }

    public function enableMerge()
    {
        if ($this->getRequest()->getParam('text_ids')) {
            return true;
        }
    }

    public function isPopover(){
        return $this->getRequest()->getParam('is_popover',false);
    }

    public function getFormUrl()
    {
        return Mage::helper('adminhtml')->getUrl('*/*/submitrollback', array('_current' => true));
    }
}