<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Model_System_Source_Packages_Status
{
    public function toOptionHash()
    {
        $result = [];

        $result[Genmato_ComposerRepo_Model_Packages::STATUS_DISABLED] = Mage::helper('genmato_composerrepo')->__('Disabled');
        $result[Genmato_ComposerRepo_Model_Packages::STATUS_ENABLED] = Mage::helper('genmato_composerrepo')->__('Enabled');
        $result[Genmato_ComposerRepo_Model_Packages::STATUS_FREE] = Mage::helper('genmato_composerrepo')->__('Free');

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