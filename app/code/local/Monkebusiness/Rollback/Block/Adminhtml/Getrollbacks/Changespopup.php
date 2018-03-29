<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 15/12/13
 * Time: 18:33
 */
class Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Changespopup extends Mage_Adminhtml_Block_Widget_Grid
{

    protected $_filterVisibility = false;
    protected $_defaultLimit    = 300;
    protected $_pagerVisibility = false;

    public function _construct()
    {
        parent::_construct();
        $this->setId('changed_attributes_grid');
        $this->setDefaultSort('change_product_id');
        $this->setUseAjax(true);
    }


    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $changeId = Mage::app()->getRequest()->getParam('change_id');
        $collection = Mage::getModel('monkebusiness_rollback/change_product')
            ->getCollection()
            ->addFieldToFilter('change_parent_id', $changeId);

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

        $this->addColumn('massaction', array(
            'header'    => Mage::helper('catalog')->__(''),
            'sortable'  => true,
            'filter'    => false,
            'index'     => 'change_product_id',
            'renderer'  => 'Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Checkbox',
            'name'      => 'change_ids'
        ));

        $this->addColumn('change_product_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'index'     => 'change_product_id',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('attribute_name', array(
            'header'    => Mage::helper('catalog')->__('Attribute Name'),
            'index'     => 'attribute_id',
            'width'     => '130px',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Attributename',
        ));


        $this->addColumn('original_value', array(
            'header'    => Mage::helper('catalog')->__('Original Value'),
            'index'     => 'original_value',
            'renderer'  => 'Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Popupvalue',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('new_value', array(
            'header'    => Mage::helper('catalog')->__('Revised Value'),
            'index'     => 'new_value',
            'renderer'  => 'Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Popupvalue',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('difflink', array(
            'index'     => 'change_product_id',
            'header'    => Mage::helper('catalog')->__('Details'),
            'renderer'  => 'Monkebusiness_Rollback_Block_Adminhtml_Getrollbacks_Renderer_Difflink',
            'width'     => '110px',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('is_rolled_back', array(
            'header'    => Mage::helper('catalog')->__('Is Restored'),
            'index'     => 'is_rolled_back',
            'type'      => 'options',
            'options'   => array(
                null => 'No',
                1 => 'Yes'
            ),
            'width'     => 100,
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('rollback_user_id', array(
            'header'    => Mage::helper('catalog')->__('Restored By'),
            'index'     => 'rollback_user_id',
            'type'      => 'options',
            'options'   => $this->getAdminUsers(),
            'filter'    => false,
            'sortable'  => false
        ));


        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('change_product_id');
        $this->getMassactionBlock()->setFormFieldName('change_ids');

        $this->getMassactionBlock()->addItem('rollback', array(
            'label'=> Mage::helper('catalog')->__('Restore'),
            'url'  => $this->getUrl('*/*/rollback',array('_current'=>true)),
            'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));


        return $this;
    }

    protected function _afterLoadCollection(){
        $disabled = array();
        foreach ($this->getCollection() as $item) {
            if ($item->getIsRolledBack()) {
                $disabled[$item->getId()] = $item->getChangeProductId();
            }
        }

        if (!empty($disabled)) {
            $this->getColumn('massaction')->setDisabledValues($disabled);
        }

        return $this;
    }

    public function getAdminUsers()
    {
        $users = Mage::getModel('admin/user')->getCollection();

        $options = array();
        foreach ($users as $user) {
           $options[$user->getId()] = $user->getFirstname() . ' ' . $user->getLastname();
        }
         return $options;
    }

    public function getRowUrl()
    {
        return false;
    }

    protected function _afterToHtml($html)
    {
        $html .= '<div id="difference_view"></div>';
        $html .= "<style type='text/css'>.massaction a,.massaction .separator {display:none}</style>";
        if ($this->getRequest()->getParam('rollback_applied')) {
            $html .= '<script type="text/javascript">parent.rollbackApplied = true</script>';
        }
        return $html;
    }

}