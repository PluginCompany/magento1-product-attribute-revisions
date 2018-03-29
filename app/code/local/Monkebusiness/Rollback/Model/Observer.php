<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 12/3/13
 * Time: 9:20 PM
 */

class Monkebusiness_Rollback_Model_Observer
{
    
    /**
     * @param $observer
     * Log changes made in product attributes
     */

    public function catalogProductSaveAfter($observer)
    {
        try{
            //retrieve modified and original product
            $product = $observer->getProduct();

            $adminSession = Mage::getSingleton('admin/session');
            $apiSession = Mage::getSingleton('api/session');

            if ($adminSession->getUser()) {
                $userId = $adminSession->getUser()->getId() . '_0';
            } elseif ($apiSession->getUser()) {
                $userId = $apiSession->getUser()->getId() . '_1';
            }

            if ($product->isObjectNew() || !$product->getOrigData() || !$userId) {
                return $this;
            }

            //load change model
            $change = Mage::getModel('monkebusiness_rollback/change');

            //execute attribute save
            $change->saveProductChange($userId, $product);
        }catch(Exception $e){
            Mage::log($e->getMessage(),null,'plugincompany_rollback.log',true);
        }
    }

    public function cataloginventoryStockItemSaveAfter(Varien_Event_Observer $observer)
    {
        try{
            if(!$this->_canSaveStockItemChange()) return;
            
            //retrieve modified and original product
            $stockItem = $observer->getEvent()->getItem();

            $adminSession = Mage::getSingleton('admin/session');
            $apiSession = Mage::getSingleton('api/session');

            if ($adminSession->getUser()) {
                $userId = $adminSession->getUser()->getId() . '_0';
            } elseif ($apiSession->getUser()) {
                $userId = $apiSession->getUser()->getId() . '_1';
            }

            //load change model
            $change = Mage::getModel('monkebusiness_rollback/change');

            //execute attribute save
            $change->saveProductChangeByStockItem($userId, $stockItem);
        }catch(Exception $e){
            Mage::log($e->getMessage(),null,'plugincompany_rollback.log',true);
        }
        
    }
    
    protected function _canSaveStockItemChange()
    {
        $request = Mage::app()->getRequest();
        if($request->getControllerModule() == "Mageplace_FastProductUpdate_Adminhtml"){
            return true;
        }
    }
    
    public function coreBlockAbstractToHtmlBefore(Varien_Event_Observer $observer)
    {
        if(!$this->_isAllowed()) return;
        
        $block = $observer->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs) {
            $block->addTab('product_changes', array(
                'label'     => Mage::helper('catalog')->__('Product Attribute Revisions'),
                'url'       => Mage::helper('adminhtml')->getUrl('adminhtml/product/rollbacks/', array('_current' => true)),
                'class'     => 'ajax',
            ));
        }
    }
    
    public function addRollbackJs(Varien_Event_Observer $observer)
    {
        if(!$this->_isAllowed()) return;
        Mage::app()
            ->getLayout()
            ->getBlock('head')
            ->addJs('monkebusiness/productrollback/product.js');
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/revisions');
    }


}