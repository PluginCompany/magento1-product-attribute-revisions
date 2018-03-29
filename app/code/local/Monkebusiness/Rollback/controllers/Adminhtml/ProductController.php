<?php
/**
 * Created by PhpStorm.
 * User: Milan
 * Date: 11/12/13
 * Time: 11:29
 */
class Monkebusiness_Rollback_Adminhtml_ProductController extends Mage_Adminhtml_Controller_Action
{
    public function rollbacksAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function popupAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function rollbackAction()
    {
        $changeIds = $this->getRequest()->getParam('change_ids');
        if(!is_array($changeIds)){
            $changeIds = explode(',',$changeIds);
        }
        $numeric = array();
        $text = array();
        $model = Mage::getModel('monkebusiness_rollback/change_product');
        foreach ($changeIds as $id) {
            $attributeType = $model->getAttributeType($id);
            if ($attributeType == 'numeric') {
                  $numeric[] = $id;
            }
            elseif ($attributeType == 'textarea') {
                $text[] = $id;
            }
        }

        if (!empty($text)) {
            $text = implode(',', $text);
        }else{
            $text = 0;
        }

        if (empty($numeric) && !$text) {
            $this->_redirect('*/*/submitrollback',array('_current'=>true,'change_ids' => implode(',',$changeIds)));
            return;
        }elseif(!empty($numeric)){
            $this->_redirect('*/*/numericoption',array('_current'=>true,'numeric_ids' => implode(',',$numeric),'change_ids'=>implode(',',$changeIds),'text_ids'=>$text));
        }else{
            $this->_redirect('*/*/textoption',array('_current'=>true,'change_ids'=>implode(',',$changeIds),'text_ids'=>$text));
        }

    }

    public function submitrollbackAction()
    {
        if (!$changes = $this->getRequest()->getParam('numeric')){
            $changes = array();
        }
        $numericJSON = $this->getRequest()->getParam('numeric_json');
        if ($numericJSON && $numericJSON != 'false') {
            $numericJSON = json_decode($numericJSON);
            foreach ($numericJSON as $n) {
                $changes[$n->id] = $n->value_to_save;
            }
        }

        if ($text = $this->getRequest()->getParam('text')) {
            $text = json_decode($text);
            foreach ($text as $t) {
                $changes[$t->id] = $t->value_to_save;
            }
        }

        $useValue = 'original';
//        if($this->getRequest()->getParam('is_popover')){
//            $useValue = 'new';
//        }

        $changeIds = $this->getRequest()->getParam('change_ids');
        if (!is_array($changeIds)) {
            $changeIds = explode(',', $changeIds);
        }

        foreach ($changeIds as $id) {
            if (!array_key_exists($id, $changes)) {
                $changes[$id] = $useValue;
            }
        }

        try {
            $successCount = Mage::getModel('monkebusiness_rollback/change_product')->rollbackMultiple($changes, $this->getRequest()->getParam('change_id'));
        } catch (Exception $e){
            Mage::getSingleton('core/session')->addError($e);
        }
        $this->_redirect('*/*/popup',
            array(
                'change_id'=>$this->getRequest()->getParam('change_id'),
                'rollback_applied'=>$successCount
            ));
        return;
    }

    public function numericoptionAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function textoptionAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function differenceviewAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('monkebusiness_rollback/adminhtml_getrollbacks_changespopup_differenceview')->setTemplate('monkebusiness/rollback/differenceview.phtml')->toHtml()
        );
    }

    public function attributehistoryAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function attributehistorygridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/revisions');
    }
}