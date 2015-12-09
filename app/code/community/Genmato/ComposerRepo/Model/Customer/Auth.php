<?php
/**
 * Default (Template) Project
 *
 * @package Genmato_Default (Template) Project
 * @author  Vladimir Kerkhoff <support@genmato.com>
 * @created 2015-12-06
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */ 
class Genmato_ComposerRepo_Model_Customer_Auth extends Mage_Core_Model_Abstract
{

    const CACHE_KEY = 'composer_repo_customer_key_';
    const CACHE_TAG = 'composer_repo_customer_';

    protected function _construct()
    {
        $this->_init('genmato_composerrepo/customer_auth');
    }

    // TODO: Send e-mail to customer with installation details
    public function sendEmail($packages)
    {
    }
}