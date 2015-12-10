<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_DownloadController extends Mage_Core_Controller_Front_Action
{
    protected function _init()
    {
        if(!$this->getRequest()->getServer('PHP_AUTH_USER')){
            $this->unAuthResponse();
            return false;
        }

        $cache = Mage::app()->getCache();

        $authKey = $this->getRequest()->getServer('PHP_AUTH_USER');
        $authSecret = $this->getRequest()->getServer('PHP_AUTH_PW');

        $customer = false;
        if (Mage::app()->useCache('packages_json')) {
            $customer = unserialize(
                $cache->load(Genmato_ComposerRepo_Model_Customer_Auth::CACHE_KEY . $authKey . '_' . $authSecret)
            );
        }

        if (!$customer) {
            $customer = Mage::getResourceModel('genmato_composerrepo/customer_auth_collection')
                ->addFieldToFilter('auth_key', $authKey)
                ->addFieldToFilter('auth_secret', $authSecret)
                ->addFieldToFilter('status', 1)
                ->getFirstItem();
            $cache->save(
                serialize($customer),
                Genmato_ComposerRepo_Model_Customer_Auth::CACHE_KEY.$authKey.'_'.$authSecret,
                array(
                    Genmato_ComposerRepo_Model_Packages::CACHE_TAG,
                    Genmato_ComposerRepo_Model_Customer_Auth::CACHE_TAG.$customer->getCustomerId()
                )
                );
        }

        if (!$customer->getId()) {
            $this->unAuthResponse();
            return false;
        }

        return $customer;
    }


    public function jsonAction()
    {
        $customer = $this->_init();
        if (!$customer) {
            return $this->sendJSON(array('status'=>false));
        }
        $json = Mage::helper('genmato_composerrepo/packages')
            ->getPackagesJson($customer->getCustomerId());

        return $this->sendJSON($json);
    }

    public function fileAction()
    {
        $customer = $this->_init();
        if (!$customer) {
            return $this->sendJSON(array('status'=>false));
        }
        return Mage::helper('genmato_composerrepo/download')
            ->getFile(
                $customer->getCustomerId(),
                $this->getRequest()->getParams()
            );
    }

    public function notifyAction()
    {
        $customer = $this->_init();
        if (!$customer) {
            return $this->sendJSON(array('status'=>false));
        }

        $download = json_decode($this->getRequest()->getRawBody(), true);
        if (!isset($download['downloads'])) {
            return $this->sendJSON(array('status'=>false));
        }
        Mage::helper('genmato_composerrepo/notify')
            ->storeNotifyData(
                $download['downloads'],
                $customer
            );

        return $this->sendJSON(array('status'=>true));
    }

    public function sendJSON($content = array())
    {
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', 'application/json', true)
            ->setHeader('Last-Modified', date('r'));

        $this->getResponse()
            ->setBody(json_encode($content, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));

        return $this;
    }

    public function unAuthResponse()
    {
        $this->getResponse()
            ->setHttpResponseCode(401)
            ->setHeader('WWW-Authenticate', 'Basic realm="Genmato Composer Repository"', true)
            ->setHeader('HTTP/1.0', '401 Unauthorized')
            ->setBody('Unauthorized Access!');
    }
}