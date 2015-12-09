<?php
/**
 * Default (Template) Project
 *
 * @package Genmato_Default (Template) Project
 * @author  Vladimir Kerkhoff <support@genmato.com>
 * @created 2015-12-06
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */ 
class Genmato_ComposerRepo_Model_Packages extends Mage_Core_Model_Abstract
{
    const CACHE_TAG = 'PACKAGES_JSON';
    const CACHE_KEY = 'composer_repo_package_';

    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
    const STATUS_FREE = 2;

    protected function _construct()
    {
        $this->_init('genmato_composerrepo/packages');
    }

    public function getByPackageName($name)
    {
        return $this->load($name, 'name');
    }
}