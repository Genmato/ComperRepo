<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Adminhtml_Composer_PackagesController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Composer'))->_title($this->__('Packages'));

        $this->loadLayout();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__('Composer'))->_title($this->__('Packages'));
        $packageId = $this->getRequest()->getParam('id', 0);

        $package = Mage::getModel('genmato_composerrepo/packages')->load($packageId);
        if (!$package->getId()) {
            $package->setStatus(Genmato_ComposerRepo_Model_Packages::STATUS_ENABLED);
        }
        Mage::register('current_package', $package);

        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $session = Mage::getSingleton('adminhtml/session');

        $packageId = $this->getRequest()->getParam('id', false);
        $data = $this->getRequest()->getPost();

        $package = Mage::getModel('genmato_composerrepo/packages');

        if($packageId) {
            $package->load($packageId);
        }
        $package->addData($data);
        try{
            $package->save();
            $session->addSuccess(Mage::helper('genmato_composerrepo')->__('The package has been saved.'));
        } catch (Exception $ex) {
            $session->addError(Mage::helper('genmato_composerrepo')->__('There was an error saving the package.'));
        }
        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/genmato_composerrepo/packages');
    }
}