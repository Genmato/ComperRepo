<?php

class Genmato_ComposerRepo_Block_Adminhtml_Composer_Customer_Packages extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->removeButton('add');

        $this->_headerText = Mage::helper('genmato_composerrepo')->__('Customer Composer Pacakges');
        $this->_blockGroup = 'genmato_composerrepo';
        $this->_controller = 'adminhtml_composer_customer_packages';

    }
}