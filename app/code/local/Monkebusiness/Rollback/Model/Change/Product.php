<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 12/3/13
 * Time: 9:10 PM
 */ 
class Monkebusiness_Rollback_Model_Change_Product extends Mage_Core_Model_Abstract
{

    protected $_change;

    protected function _construct()
    {
        $this->_init('monkebusiness_rollback/change_product');
    }

    public function getAttributeType($changeId)
    {
        $this->load($changeId);

        if($this->getIsStockitem()){
            return $this->_getStockItemAttributeType();
        }

        if (!$attrID = $this->getAttributeId()) {
            throw new Exception ('Invalid product change ID specified');
        }

        //load new attribute
        if (!$attr = Mage::getModel('eav/entity_attribute')->load($attrID)) {
            throw new Exception ('Invalid attribute ID specified');
        }

        if (in_array($attr->getBackendType(), array('int', 'decimal'))
            && in_array($attr->getFrontendInput(),array('text','price','weight'))){
            return 'numeric';
        }
        if ($attr->getFrontendInput() == 'textarea'){
            return 'textarea';
        }
        return false;
    }

    protected function _getStockItemAttributeType()
    {
        if(stristr($this->getAttributeCode(),'use_config')){
            return false;
        }
        if (in_array($this->getAttributeCode(), array('is_qty_decimal','backorders','is_in_stock','manage_stock','enable_qty_increments','is_decimal_divided'))) {
            return false;
        }
        return 'numeric';
    }

    protected function _getPreparedNewValue($newValue,$attrCode){
        $frontendInput = $this->getAttributeFrontendInputByCode($attrCode);
        if($frontendInput == 'multiselect' || $attrCode == 'category_ids'){
            $newValue = explode(',',$newValue);
        }
        return $newValue;
    }

    public function rollbackMultiple($changes,$changeId)
    {
        $change = Mage::getModel('monkebusiness_rollback/change')->load($changeId);
        $product = Mage::getModel('catalog/product')->setStoreId($change->getStoreId())->load($change->getChangedEntityId());

        $successIds = array();
        $stockData = array();

        foreach ($changes as $k => $v) {
            //load change
            $this->load($k);
            $attributeCode = $this->getAttributeCode();

            if ($v == 'difference') {
                //difference rollback
                //get difference between values
                $difference  = $this->getNewValue() - $this->getOriginalValue();

                if($this->getIsStockitem()){
                    $attrValue = $product->getStockItem()->getData($attributeCode);
                }else{
                    $attrValue = $product->getData($attributeCode);
                }

                $newValue = $attrValue - $difference;

                //don't allow negative values
                if ($newValue < 0) {
                    Mage::getSingleton('core/session')->addError('Unable to restore difference because outcome would be negative');
                    continue;
                }

            }elseif($v == 'original'){
                //regular rollback
                $newValue = $this->getOriginalValue();
            }elseif($v == 'new'){
                $newValue = $this->getNewValue();
            }else{
                $newValue = $v;
            }

            $successIds[] = $k;

            if($this->getIsStockitem()){
                $stockData[$attributeCode] = $newValue;
                $product->setStockData($stockData);
            }else{
                $newValue = $this->_getPreparedNewValue($newValue,$attributeCode);
                $product->setData($attributeCode, $newValue);
            }
        }

        try {
            Mage::register('original_change_id', $changeId);
            $product->save();
        }
        catch (Exception $e){
            throw new Exception('Unable to save product');
            return;
        }

        $userId = Mage::getSingleton('admin/session')->getUser()->getUserId();
        //save rollbacks and create new changes
        foreach ($successIds as $id) {
            $this->load($id);
            $this
                ->setIsRolledBack(1)
                ->setRollbackUserId($userId)
                ->save();
        }

        $count = count($successIds);
        Mage::getSingleton('core/session')->addSuccess('Successfully restored ' . $count . ' change(s). ');
        return $count;
    }

    public function getChange()
    {
        if(!$this->_change){
            $this->_change = Mage::getModel('monkebusiness_rollback/change')->load($this->getChangeParentId());
        }
        return $this->_change;
    }

    public function getAttributeChangeCount($productId){
        $collection = Mage::getModel('monkebusiness_rollback/change_product')->getCollection();
        $collection
            ->addFieldToFilter('product_id',$productId)
            ->getSelect()
            ->group(array('attribute_id','attribute_code'))
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                'attribute_code',
                'is_stockitem',
                'count' => new Zend_Db_Expr("COUNT('*')")
            ));
        return $collection;
    }

    public function getAttributeTypeByCode($attributeCode)
    {
        $type = Mage::getModel("eav/config")->getAttribute('catalog_product', $attributeCode)->getBackendType();
        return $type;
    }

    public function getAttributeIdByCode($attributeCode)
    {
        $id = Mage::getModel("eav/config")->getAttribute('catalog_product', $attributeCode)->getAttributeId();
        return $id;
    }

    public function getAttributeFrontendInputByCode($attributeCode){
        return Mage::getModel("eav/config")->getAttribute('catalog_product', $attributeCode)->getFrontendInput();
    }

}