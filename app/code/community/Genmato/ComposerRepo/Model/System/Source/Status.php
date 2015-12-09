<?php

class Genmato_ComposerRepo_Model_System_Source_Status
{
    public function toOptionHash()
    {
        $result = [];

        $result[Genmato_ComposerRepo_Model_Packages::STATUS_DISABLED] = Mage::helper('genmato_composerrepo')->__('Disabled');
        $result[Genmato_ComposerRepo_Model_Packages::STATUS_ENABLED] = Mage::helper('genmato_composerrepo')->__('Enabled');

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