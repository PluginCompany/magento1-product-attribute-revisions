<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 12/3/13
 * Time: 9:09 PM
 */ 
class Monkebusiness_Rollback_Model_Change extends Mage_Core_Model_Abstract
{

    protected $_product;
    protected $_modifiedAttributes;
    protected $_productChangeModel;
    protected $_skip = array(
        'created_at',
        'updated_at',
        'media_gallery',
        'stock_item',
        'stock_data',
        '_cache_editable_attributes',
        'website_ids',
        'use_config_gift_message_available',
        'store_ids',
        'special_to_date_is_formated',
        'special_from_date_is_formated',
        'parent_id',
        'news_to_date_is_formated',
        'news_from_date_is_formated',
        'media_attributes',
        'custom_design_to_is_formated',
        'custom_design_from_is_formated',
        'is_changed_categories',
        'affected_category_ids',
        'stock_status_changed_automatically',
        'stock_status_changed_auto',
        'url_key_create_redirect',
        'save_rewrites_history',
        'related_link_data',
        'tier_price',
        'tier_price_changed',
        'group_price',
        'group_price_changed',
        'product_type_id',
        'original_inventory_qty',
        'qty_correction',
        'store_id',
        'product_name'
    );

    protected function _construct()
    {
        $this->_init('monkebusiness_rollback/change');
        $this->_modifiedAttributes = new Varien_Data_Collection();
    }

    protected function _getProductChangeModel(){
        if(!$this->_productChangeModel){
            $this->_productChangeModel = Mage::getModel('monkebusiness_rollback/change_product');
        }
        return $this->_productChangeModel;
    }


    protected function _isAttrChanged($new,$original,$attrCode = false){
        if($attrCode == 'category_ids'){
            return $this->_product->getIsChangedCategories();
        }
        $new = $this->_getStringValue($new);
        $original = $this->_getStringValue($original);
        return $new != $original;
    }

    protected function _getStringValue($value){
        if(is_array($value)){
            sort($value);
            $value = implode(',',$value);
        }
        if(is_numeric($value)){
            $num = explode('.',$value);
            if(!isset($num[1]) || $num[1] == 0){
                $value = number_format($value,0,'','');
            }else{
                $value = number_format($value,2,'.','');
            }
        }
        if($value === null){
            $value = "";
        }
        return $value;
    }

    protected function _getOldCatIds(){
        if(!$this->_product->getIsChangedCategories()){
            return null;
        }
        $newCatIds = $this->_product->getCategoryIds();
        $changed = $this->_product->getAffectedCategoryIds();
        $oldCatIds = $newCatIds;
        foreach($changed as $catId){
            if(in_array($catId,$newCatIds)){
                unset($oldCatIds[array_search($catId,$oldCatIds)]);
            }else{
                $oldCatIds[] = $catId;
            }
        }
        return $oldCatIds;
    }

    protected function _getOrigData($attrCode){
        if($attrCode == 'category_ids'){
            return $this->_getOldCatIds();
        }
        return $this->_product->getOrigData($attrCode);
    }

    protected function _addAttrChange($attrCode,$new,$original,$stockItem = false){
        $item = Mage::getModel('monkebusiness_rollback/change_product');
        $item
            ->setProductId($this->_product->getId())
            ->setIsStockitem($stockItem)
            ->setAttributeId($this->_getProductChangeModel()->getAttributeIdByCode($attrCode,$stockItem))
            ->setAttributeCode($attrCode)
            ->setOriginalValue($this->_getStringValue($original))
            ->setNewValue($this->_getStringValue($new));
        $this->_modifiedAttributes->addItem($item);
    }

    protected function _addRegularAttributes(){
        //add normal attributes
        foreach($this->_product->getData() as $attrCode => $new){
            //skip some attributes
            if(in_array($attrCode,$this->_skip)){
                continue;
            }

            $original =  $this->_getOrigData($attrCode);

            if(!$this->_isAttrChanged($new,$original,$attrCode)){
                continue;
            }

            //add attribute change model to collection
            $this->_addAttrChange($attrCode,$new,$original);

            $this->setHasChanges(true);
        }
        return $this;
    }

    protected function _addStockItemAttributes(){
        //add stock item attributes
        $stockItem = $this
            ->_product
            ->getStockItem()
        ;
        if (!$stockItem || !is_object($stockItem)) {
            return $this;
        }
        foreach ($stockItem->getData() as $attrCode => $new) {

            if(in_array($attrCode,$this->_skip)){
                continue;
            }

            //if original data doesn't exitst skip
            $oData = $stockItem->getOrigData();
            if (!$oData && !isset($oData[$attrCode])) {
                continue;
            }

            $original =  $stockItem->getOrigData($attrCode);

            if(!$this->_isAttrChanged($new,$original)){
                continue;
            }

            //add attribute change model to collection
            $this->_addAttrChange($attrCode,$new,$original,true);

            $this->setHasChanges(true);
        }
        return $this;
    }

    public function saveProductChange($userId,$product)
    {
        $this->_product = $product;
        $originalChangeId = null;
        $isApiCall = Mage::getSingleton('api/session')->getUser();
        if(!$this->_product->getData('_edit_mode') && !$isApiCall) {
            return;
        }

        if (Mage::registry('original_change_id')) {
            $originalChangeId = Mage::registry('original_change_id');
        }

        $this
            ->_addRegularAttributes()
            ->_addStockItemAttributes()
        ;

        if ($this->getHasChanges()){
            $this
                ->setChangeTypeId(1)
                ->setAdminUserId($userId)
                ->setChangedEntityId($this->_product->getId())
                ->setChangeDate(date('Y-m-d H:i:s'))
                ->setOriginalChangeId($originalChangeId)
                ->setStoreId($this->_product->getStoreId())
                ->save();
            ;
            $this->_saveProductAttributes();
        }

        return $this;
    }
    
    public function saveProductChangeByStockItem(
        $userId,
        Mage_CatalogInventory_Model_Stock_Item $stockItem
    ) {
        $this->_product = Mage::getSingleton('catalog/product');
        $this->_product
                ->unsetData()
                ->setProductId($stockItem->getProductId())
                ->setId($stockItem->getProductId())
                ->setStockItem($stockItem)
        ;
        
        $originalChangeId = null;

        if (Mage::registry('original_change_id')) {
            $originalChangeId = Mage::registry('original_change_id');
        }

        $this->_addStockItemAttributes();

        if ($this->getHasChanges()) {
            $this
                ->setChangeTypeId(1)
                ->setAdminUserId($userId)
                ->setChangedEntityId($this->_product->getId())
                ->setChangeDate(date('Y-m-d H:i:s'))
                ->setOriginalChangeId($originalChangeId)
                ->setStoreId($this->_product->getStoreId())
                ->save();;
            $this->_saveProductAttributes();
        }

        return $this;
    }


    protected function _saveProductAttributes()
    {
        foreach($this->_modifiedAttributes as $item){
            $item
                ->setChangeParentId($this->getId())
                ->save();
        }
        return $this;
    }
}