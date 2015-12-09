<?php

class Genmato_ComposerRepo_Block_Adminhtml_Composer_Packages_Versions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('composerrepo_packages_versions');
        $this->setUseAjax(true);
        $this->setDefaultSort('createdate');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('genmato_composerrepo/packages_versions_collection')
            ->addFieldToFilter('package_id', array('eq'=>$this->getRequest()->getParam('id', 0)));
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
            'version',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Version'),
                'width' => '200px',
                'index' => 'version',
            )
        );


        $this->addColumn(
            'file',
            array(
                'header' => Mage::helper('genmato_composerrepo')->__('Filename'),
                'index' => 'file',
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