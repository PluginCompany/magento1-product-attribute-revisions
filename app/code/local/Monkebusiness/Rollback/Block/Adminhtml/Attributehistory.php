<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 11/12/13
 * Time: 11:42
 */
class Monkebusiness_Rollback_Block_Adminhtml_Attributehistory extends Mage_Adminhtml_Block_Widget_Grid
{   /**
 * Set grid params
 *
 */

    public function __construct()
    {
        parent::__construct();
        $this->setId('attribute_history_grid');
        $this->setDefaultSort('change_product_id');
        $this->setUseAjax(true);
        $this->setTemplate('widget/grid.phtml');
        $this->setDefaultDir('desc');
        $this->setDefaultSort('change_date');
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $productId = Mage::app()->getRequest()->getParam('id');
        $attributeCode = Mage::app()->getRequest()->getParam('attribute_code');

        $stockItem = false;
        if(stristr($attributeCode,'inventory_')){
            $stockItem = true;
            $attributeCode = str_replace('inventory_','',$attributeCode);
        }

        $store = 0;
        if ($storeId = $this->getRequest()->getParam('store')) {
            $store = $storeId;
        }
        $changeTypeId = 1;
        $collection = Mage::getModel('monkebusiness_rollback/change_product')->getCollection();
        $collection
            ->getSelect()
            ->joinLeft(array('change'=>$collection->getTable('monkebusiness_rollback/change')),'main_table.change_parent_id=change.change_id')
        ;
        $collection
            ->addFieldToFilter('product_id',$productId)
            ->addFieldToFilter('store_id',$store)
            ->addFieldToFilter('attribute_code',$attributeCode)
            ->addFieldToFilter('is_stockitem',$stockItem)
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('change_date', array(
            'header'    => Mage::helper('catalog')->__('Change date'),
            'width'     => 100,
            'index'     => 'change_date',
            'type'      => 'date',
        ));

        $this->addColumn('username', array(
            'header'    => Mage::helper('catalog')->__('User'),
            'index'     => 'admin_user_id',
            'width'     => 100,
            'type'      => 'options',
            'options'   => Mage::helper('monkebusiness_rollback')->getAdminUsers()
        ));

        $this->addColumn('original_value', array(
            'header'    => Mage::helper('catalog')->__('Original Value'),
            'index'     => 'original_value',
            'renderer'  => 'Monkebusiness_Rollback_Block_Adminhtml_Attributehistory_Renderer_Value',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('new_value', array(
            'header'    => Mage::helper('catalog')->__('Revised Value'),
            'index'     => 'new_value',
            'renderer'  => 'Monkebusiness_Rollback_Block_Adminhtml_Attributehistory_Renderer_Value',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('action',array(
                'header'    => "",
                'width'     => '50px',
                'index'     => 'change_id',
                'renderer'  => 'Monkebusiness_Rollback_Block_Adminhtml_Attributehistory_Renderer_Restore',
                'filter'    => false,
                'sortable'  => false
            )
        );


        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('adminhtml/product/attributehistorygrid', array('_current'=>true));
    }

    public function getRowUrl($item)
    {
        return false;
    }
}