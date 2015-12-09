<?php

class Genmato_ComposerRepo_Block_Customer_Keys extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $customer = $this->getCustomer();

        $collection = Mage::getResourceModel('genmato_composerrepo/customer_auth_collection')
            ->addFieldToFilter('status', array('eq'=>1))
            ->addFieldToFilter('customer_id', array('eq'=>$customer->getId()));


        $this->setAuthKeys($collection);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'composerrepo.keys.pager')
            ->setCollection($this->getAuthKeys());
        $this->setChild('pager', $pager);
        $this->getAuthKeys()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getDeleteUrl($key)
    {
        return $this->getUrl('*/*/delete', array('id' => $key->getId()));
    }

    public function getGenerateUrl()
    {
        return $this->getUrl('*/*/save');
    }

    protected function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
}