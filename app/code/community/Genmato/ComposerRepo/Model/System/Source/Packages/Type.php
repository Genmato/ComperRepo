<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Model_System_Source_Packages_Type
{
    public function toOptionHash()
    {
        $result = [];

        $result[Genmato_ComposerRepo_Model_Packages::PACKAGE_NORMAL] = Mage::helper('genmato_composerrepo')->__('Normal (Payed)');
        $result[Genmato_ComposerRepo_Model_Packages::PACKAGE_BUNDLE] = Mage::helper('genmato_composerrepo')->__('Bundled (Library)');

        return $result;
    }

    public function toOptionArray()
    {
        $result = [];

        foreach ($this->toOptionHash() as $key=>$val) {
            $result[] = array('value'=>$key, 'label'=>$val);
        }

        return $result;
    }
}