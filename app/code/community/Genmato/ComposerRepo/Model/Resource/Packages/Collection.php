<?php
/**
 * Default (Template) Project
 *
 * @package Genmato_Default (Template) Project
 * @author  Vladimir Kerkhoff <support@genmato.com>
 * @created 2015-12-06
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */ 
class Genmato_ComposerRepo_Model_Resource_Packages_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('genmato_composerrepo/packages');
    }

    public function toOptionHash()
    {
        return parent::_toOptionHash('entity_id', 'name');
    }

}