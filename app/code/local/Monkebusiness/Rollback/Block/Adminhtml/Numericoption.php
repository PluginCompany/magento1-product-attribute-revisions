<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Product Attribute Revisions, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Monkebusiness_Rollback_Block_Adminhtml_Numericoption extends Mage_Core_Block_Template
{
    protected $_productId;

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('monkebusiness/rollback/numericoption.phtml');
    }

    public function getNumericOptionArray()
    {
        $optionIds = $this->getRequest()->getParam('numeric_ids');
        if (!is_array($optionIds)) {
            $optionIds = explode(',', $optionIds);
        }
        $changeId = $this->getRequest()->getParam('change_id');

        $change = Mage::getModel('monkebusiness_rollback/change')->load($changeId);
        $productId = $change->getChangedEntityId();


        $values = array();

        foreach ($optionIds as $id) {
            $change = Mage::getModel('monkebusiness_rollback/change_product')->load($id);
            if(!$change->getIsStockitem()){
                $currentValue = Mage::getResourceModel('catalog/product')
                    ->getAttributeRawValue(
                        $change->getProductId(),
                        $change->getAttributeCode(),
                        $change->getChange()->getStoreId()
                    );
            }else{
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($change->getProductId());
                $currentValue = $stockItem->getData($change->getAttributeCode());
            }
            $values[$id] = array();
            $values[$id]['original_value'] = number_format($change->getOriginalValue(),2);
            $values[$id]['new_value'] = number_format($change->getNewValue(),2);
            $values[$id]['difference'] = number_format($change->getNewValue() - $change->getOriginalValue(),2);
            $values[$id]['current_value'] = number_format($currentValue);
            $values[$id]['attribute_code'] = $change->getAttributeCode();
        }

        return $values;
    }

    public function getSubmitUrl()
    {
        if ($this->getRequest()->getParam('text_ids')) {
            return Mage::helper('adminhtml')->getUrl('*/*/textoption', array('_current' => true));
        }
        return Mage::helper('adminhtml')->getUrl('*/*/submitrollback', array('_current' => true));
    }

    public function getSubmitText()
    {
        if ($this->getRequest()->getParam('text_ids')) {
            return 'Continue';
        }else{
            return 'Submit';
        }
    }

}