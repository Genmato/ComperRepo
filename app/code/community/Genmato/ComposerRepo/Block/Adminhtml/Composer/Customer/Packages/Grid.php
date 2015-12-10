<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Block_Adminhtml_Composer_Customer_Packages_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('composerrepo_customer_packages');
        $this->setUseAjax(true);
        $this->setDefaultSort('createdate');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('genmato_composerrepo/customer_packages_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('ID'),
                'align' => 'right',
                'width' => '120px',
                'filter_index' => 'entity_id',
                'index' => 'entity_id',
            )
        );

        $this->addColumn(
            'createdate',
            array(
                'header' => Mage::helper('genmato_composerrepo')
                    ->__('Created at'),
                'align' => 'left',
                'width' => '160px',
                'type' => 'datetime',
                'filter_index' => 'createdate',
                'index' => 'createdate',
            )
        );

        $this->addColumn(
            'status',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Status'),
                'type' => 'options',
                'width' => '80px',
                'index' => 'status',
                'options' => Mage::getSingleton('genmato_composerrepo/system_source_status')->toOptionHash()

            )
        );

        $this->addColumn(
            'customer_id',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Customer ID'),
                'index' => 'customer_id',
                'width' => '80px',
            )
        );

        $this->addColumn(
            'order_id',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Order ID'),
                'index' => 'order_id',
            )
        );

        $this->addColumn(
            'package_id',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Package'),
                'type' => 'options',
                'options' => Mage::getResourceModel('genmato_composerrepo/packages_collection')->toOptionHash(),
                'index' => 'package_id',
            )
        );

        $this->addColumn(
            'last_allowed_version',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Last Allowed Version'),
                'index' => 'last_allowed_version',
            )
        );

        $this->addColumn(
            'last_allowed_date',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Updates End Date'),
                'type' => 'datetime',
                'index' => 'last_allowed_date',
            )
        );

        return $this;
    }

    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}