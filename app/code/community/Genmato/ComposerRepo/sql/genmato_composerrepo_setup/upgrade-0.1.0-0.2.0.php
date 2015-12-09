<?php

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