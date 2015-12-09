<?php

class Genmato_ComposerRepo_Adminhtml_Composer_StatisticsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/genmato_composerrepo/statistics');
    }
}