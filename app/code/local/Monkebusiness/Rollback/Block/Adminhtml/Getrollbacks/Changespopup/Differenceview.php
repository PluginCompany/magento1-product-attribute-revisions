<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Attribute Revisions, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Changespopup_Differenceview extends Mage_Core_Block_Template
{
    protected $_change;

    public function getChangeItem()
    {
        if ($this->_change) {
            return $this->_change;
        }
        $id = $this->getRequest()->getParam('id');
        $change = Mage::getModel('monkebusiness_rollback/change_product')->load($id);
        $this->_change = $change;
        return $change;
    }

    public function enableMerge()
    {
        return $this->getRequest()->getParam('enable_merge');
    }

    public function getValueOne()
    {
        return $this->getChangeItem()->getOriginalValue();
    }

    public function getValueTwo()
    {
        $changeItem = $this->getChangeItem();
        return $this->getChangeItem()->getNewValue();
    }

    public function isPopover(){
        return $this->getRequest()->getParam('is_popover',false);
    }
}