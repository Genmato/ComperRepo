<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
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