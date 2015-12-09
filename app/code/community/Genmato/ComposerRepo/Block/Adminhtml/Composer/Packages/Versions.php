<?php

class Genmato_ComposerRepo_Block_Adminhtml_Composer_Packages_Versions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->removeButton('add');
        $this->_addBackButton();

        $this->_headerText = Mage::helper('genmato_composerrepo')->__('Composer Packages Versions');
        $this->_blockGroup = 'genmato_composerrepo';
        $this->_controller = 'adminhtml_composer_packages_versions';
    }

    public function getBackUrl()
    {
        return $this->getUrl('adminhtml/composer_packages/');
    }
}