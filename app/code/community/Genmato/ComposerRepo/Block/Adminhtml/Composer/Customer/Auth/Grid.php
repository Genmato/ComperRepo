<?php

class Genmato_ComposerRepo_Block_Adminhtml_Composer_Customer_Auth_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('composerrepo_customer_auth');
        $this->setUseAjax(true);
        $this->setDefaultSort('createdate');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('genmato_composerrepo/customer_auth_collection');
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
            'description',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Description'),
                'index' => 'description',
            )
        );

        $this->addColumn(
            'auth_key',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Key'),
                'index' => 'auth_key',
            )
        );

        $this->addColumn(
            'auth_secret',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Secret'),
                'index' => 'auth_secret',
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