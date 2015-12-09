<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()->dropTable($installer->getTable('genmato_composerrepo/packages'));
$table = $installer->getConnection()->newTable($installer->getTable('genmato_composerrepo/packages'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'auto_increment' => true,
        ),
        'Entity Id'
    )
    ->addColumn('createdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Create date')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Status')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Product Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(), 'Package Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(), 'Package description')
    ->addColumn('repository_url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 150, array(), 'Repository URL')
    ->addColumn('repository_options', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Repository Options JSON')
    ->addColumn('package_json', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Generated package JSON')
    ->addForeignKey(
        $installer->getFkName('genmato_composerrepo/packages', 'product_id', 'catalog/product', 'entity_id'),
        'product_id',
        $installer->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Composer Packages');
$installer->getConnection()->createTable($table);

$installer->getConnection()->dropTable($installer->getTable('genmato_composerrepo/packages_versions'));
$table = $installer->getConnection()->newTable($installer->getTable('genmato_composerrepo/packages_versions'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'auto_increment' => true,
        ),
        'Entity Id'
    )
    ->addColumn('createdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Create date')
    ->addColumn('package_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Package ID')
    ->addColumn('version', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(), 'Package Version')
    ->addColumn('file', Varien_Db_Ddl_Table::TYPE_VARCHAR, 250, array(), 'File Name')
    ->addIndex(
        $installer->getIdxName('genmato_composerrepo/packages_versions', array('package_id')),
        array('package_id')
    )
    ->addIndex(
        $installer->getIdxName('genmato_composerrepo/packages_versions', array('version')),
        array('version')
    )
    ->addForeignKey(
        $installer->getFkName('genmato_composerrepo/packages_versions', 'package_id', 'genmato_composerrepo/packages', 'entity_id'),
        'package_id',
        $installer->getTable('genmato_composerrepo/packages'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Composer Packages Versions');
$installer->getConnection()->createTable($table);

$installer->getConnection()->dropTable($installer->getTable('genmato_composerrepo/packages_notify'));
$table = $installer->getConnection()->newTable($installer->getTable('genmato_composerrepo/packages_notify'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'auto_increment' => true,
        ),
        'Entity Id'
    )
    ->addColumn('createdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Create date')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Customer ID')
    ->addColumn('package_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Package')
    ->addColumn('version_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Version')
    ->addColumn('remote_ip', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(), 'Remote IP')
    ->addIndex(
        $installer->getIdxName('genmato_composerrepo/packages_versions', array('package_id')),
        array('package_id')
    )
    ->addIndex(
        $installer->getIdxName('genmato_composerrepo/packages_versions', array('customer_id')),
        array('customer_id')
    )
    ->addIndex(
        $installer->getIdxName('genmato_composerrepo/packages_versions', array('version_id')),
        array('version_id')
    )
    ->addForeignKey(
        $installer->getFkName('genmato_composerrepo/packages_notify', 'version_id', 'genmato_composerrepo/packages_versions', 'entity_id'),
        'version_id',
        $installer->getTable('genmato_composerrepo/packages_versions'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('genmato_composerrepo/packages_notify', 'version_id', 'genmato_composerrepo/packages', 'entity_id'),
        'package_id',
        $installer->getTable('genmato_composerrepo/packages'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('genmato_composerrepo/packages_notify', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Composer Packages Download Notify');
$installer->getConnection()->createTable($table);

$installer->getConnection()->dropTable($installer->getTable('genmato_composerrepo/customer_auth'));
$table = $installer->getConnection()->newTable($installer->getTable('genmato_composerrepo/customer_auth'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'auto_increment' => true,
        ),
        'Entity Id'
    )
    ->addColumn('createdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Create date')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Status')
    ->addColumn('default', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'default')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Customer ID')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(), 'Key description')
    ->addColumn('auth_key', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(), 'Auth Key')
    ->addColumn('auth_secret', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(), 'Auth Secret')
    ->addIndex(
        $installer->getIdxName('genmato_composerrepo/customer_auth', array('customer_id')),
        array('customer_id')
    )
    ->addIndex(
        $installer->getIdxName('genmato_composerrepo/customer_auth', array('auth_key')),
        array('auth_key')
    )
    ->addIndex(
        $installer->getIdxName('genmato_composerrepo/customer_auth', array('auth_secret')),
        array('auth_secret')
    )
    ->addForeignKey(
        $installer->getFkName('genmato_composerrepo/customer_auth', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Customer Repo Authentication');
$installer->getConnection()->createTable($table);

$installer->getConnection()->dropTable($installer->getTable('genmato_composerrepo/customer_packages'));
$table = $installer->getConnection()->newTable($installer->getTable('genmato_composerrepo/customer_packages'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'auto_increment' => true,
        ),
        'Entity Id'
    )
    ->addColumn('createdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Create date')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Status')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Customer ID')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Order ID')
    ->addColumn('package_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Package ID')
    ->addIndex(
        $installer->getIdxName('genmato_composerrepo/customer_packages', array('customer_id')),
        array('customer_id')
    )
    ->addIndex(
        $installer->getIdxName('genmato_composerrepo/customer_packages', array('order_id')),
        array('order_id')
    )
    ->addIndex(
        $installer->getIdxName('genmato_composerrepo/customer_packages', array('package_id')),
        array('package_id')
    )
    ->addForeignKey(
        $installer->getFkName('genmato_composerrepo/customer_packages', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('genmato_composerrepo/customer_packages', 'package_id', 'genmato_composerrepo/packages', 'entity_id'),
        'package_id',
        $installer->getTable('genmato_composerrepo/packages'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Customer Ordered Packages');
$installer->getConnection()->createTable($table);



