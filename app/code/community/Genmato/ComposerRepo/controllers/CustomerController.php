<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_CustomerController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        if (!$this->_getSession()->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }

    /**
     * Action postdispatch
     *
     * Remove No-referer flag from customer session after each action
     */
    public function postDispatch()
    {
        parent::postDispatch();
        $this->_getSession()->unsNoReferer(false);
    }

    public function testAction()
    {
        $key = Mage::getModel('genmato_composerrepo/customer_auth')->load(2);

        $packages = Mage::getResourceModel('genmato_composerrepo/packages_collection');

        $key->sendEmail($packages);
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function saveAction()
    {
        $desciption = $this->getRequest()->getPost('description', 'New Key');
        $customerId = $this->_getSession()->getCustomer()->getId();

        $authkey = Mage::getModel('genmato_composerrepo/customer_auth')
            ->setStatus(1)
            ->setDefault(0)
            ->setCustomerId($customerId)
            ->setDescription($desciption)
            ->setAuthKey(Mage::helper('core')->getRandomString(32))
            ->setAuthSecret(Mage::helper('core')->getRandomString(32));

        try {
            $authkey->save();
            $this->_getSession()->addSuccess($this->__('New authentication key generated'));
        } catch (Exception $ex) {
            $this->_getSession()->addError($this->__('Unable to save new authentication key'));
        }
        $this->_redirect('*/*/index');
    }

    public function deleteAction()
    {
        $keyId = $this->getRequest()->getParam('id');
        $authkey = Mage::getModel('genmato_composerrepo/customer_auth')->load($keyId);
        if (!$authkey->getId() || $authkey->getCustomerId()!=$this->_getSession()->getCustomer()->getId()) {
            $this->_getSession()->addError($this->__('Invalid key'));
            $this->_redirect('*/*/index');
        }

        try {
            $authkey->setStatus(0)->save();
            $this->_getSession()->addSuccess($this->__('Authentication key deleted'));
        } catch (Exception $ex) {
            $this->_getSession()->addError($this->__('Unable to delete authentication key'));
        }
        $this->_redirect('*/*/index');
    }

    public function packagesAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }
}