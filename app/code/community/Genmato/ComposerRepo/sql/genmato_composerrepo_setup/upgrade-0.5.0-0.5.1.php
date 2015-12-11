<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addIndex(
        $installer->getTable('genmato_composerrepo/customer_auth'),
        $installer->getIdxName(
            'genmato_composerrepo/customer_auth',
            array('auth_key','auth_secret'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('auth_key','auth_secret'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    );

$installer->endSetup();