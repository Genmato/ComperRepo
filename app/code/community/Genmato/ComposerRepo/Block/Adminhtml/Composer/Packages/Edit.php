<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Block_Adminhtml_Composer_Packages_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'genmato_composerrepo';
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_composer_packages';

        if(!Mage::registry('current_package')->getId()) {
            $this->_removeButton('delete');
        }
    }

    public function getHeaderText()
    {
        if(!is_null(Mage::registry('current_package')->getId())) {
            return Mage::helper(
                'genmato_composerrepo')->__('Edit Magento 2 Composer Package "%s"',
                $this->escapeHtml(Mage::registry('current_package')->getName())
            );
        } else {
            return Mage::helper('genmato_composerrepo')->__('New Magento 2 Composer Package');
        }
    }

}