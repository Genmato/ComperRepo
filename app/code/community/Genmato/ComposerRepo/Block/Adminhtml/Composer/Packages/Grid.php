<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Block_Adminhtml_Composer_Packages_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('composerrepo_packages');
        $this->setUseAjax(true);
        $this->setDefaultSort('createdate');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('genmato_composerrepo/packages_collection');
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
                'options' => Mage::getSingleton('genmato_composerrepo/system_source_packages_status')->toOptionHash()

            )
        );

        $this->addColumn(
            'description',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Description'),
                'index' => 'description',
            )
        );

        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Name'),
                'index' => 'name',
            )
        );

        $this->addColumn(
            'version',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Version'),
                'width' => '80px',
                'index' => 'version',
            )
        );

        $this->addColumn(
            'bundled_package',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Type'),
                'type' => 'options',
                'width' => '150px',
                'index' => 'bundled_package',
                'options' => Mage::getSingleton('genmato_composerrepo/system_source_packages_type')->toOptionHash()

            )
        );

        $this->addColumn(
            'action',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Action'),
                'width' => '75px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('genmato_composerrepo')->__('Edit'),
                        'url' => array('base' => 'adminhtml/composer_packages/edit'),
                        'field' => 'id'
                    ),
                    array(
                        'caption' => Mage::helper('genmato_composerrepo')->__('Versions'),
                        'url' => array('base' => 'adminhtml/composer_packages_versions/'),
                        'field' => 'id'
                    ),
                ),
                'filter' => false,
                'sortable' => false
            )
        );

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}