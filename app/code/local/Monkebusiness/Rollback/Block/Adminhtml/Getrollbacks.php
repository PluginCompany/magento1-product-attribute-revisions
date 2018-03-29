<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 11/12/13
 * Time: 11:42
 */
class Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks extends Mage_Adminhtml_Block_Widget_Grid
{   /**
 * Set grid params
 *
 */

    public function __construct()
    {
        parent::__construct();
        $this->setId('rollback_grid');
        $this->setDefaultSort('change_id');
        $this->setUseAjax(true);
        $this->setTemplate('widget/grid.phtml');
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $productId = Mage::app()->getRequest()->getParam('id');
        $store = 0;
        if ($storeId = $this->getRequest()->getParam('store')) {
            $store = $storeId;
        }
        $changeTypeId = 1;
        $collection = Mage::getModel('monkebusiness_rollback/change')->getCollection();
        $collection
            ->addFieldToFilter('changed_entity_id',$productId)
            ->addFieldToFilter('change_type_id',$changeTypeId)
            ->addFieldToFilter('store_id',$store)
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

        $this->addColumn('change_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'change_id'
        ));

        $this->addColumn('original_change_id', array(
            'header'    => Mage::helper('catalog')->__('Original ID'),
            'width'     => 60,
            'index'     => 'original_change_id',
        ));

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

        $this->addColumn('attributes', array(
            'header'    => Mage::helper('catalog')->__('Attributes'),
            'index'     => 'change_id',
            'renderer'  => 'Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Attributes',
            'filter'    => false,
            'sortable'  => false
        ));
        $this->addColumn('original_value', array(
            'header'    => Mage::helper('catalog')->__('Original Value'),
            'index'     => 'original_value',
            'renderer'  => 'Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Value',
            'filter'    => false,
            'sortable'  => false,
        ));
        $this->addColumn('new_value', array(
            'header'    => Mage::helper('catalog')->__('Revised Value'),
            'index'     => 'new_value',
            'renderer'  => 'Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Value',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('action',array(
                'header'    => Mage::helper('sales')->__('Details'),
                'width'     => '80px',
                'index'     => 'change_id',
                'renderer'  => 'Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Popuplink',
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
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('adminhtml/product/rollbacks', array('_current'=>true));
    }

    public function getRowUrl($item)
    {
        return false;
    }
}