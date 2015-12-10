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
    ->addColumn(
        $installer->getTable('genmato_composerrepo/packages'),
        'version',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => '50',
            'nullable' => true,
            'comment' => 'Last version number'
        )
    );

$installer->endSetup();