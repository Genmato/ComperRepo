<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Helper_Packages extends Genmato_ComposerRepo_Helper_Data
{
    /**
     * Build /packages.json file for customer with allowed packages
     *
     * @param $customerId
     * @return array
     */
    public function getPackagesJson($customerId)
    {
        $config = [];
        $config['notify-batch'] = Mage::getUrl('composer/download/notify');

        $cache = Mage::app()->getCache();
        $cacheTags = [];
        $cacheTags[] = Genmato_ComposerRepo_Model_Packages::CACHE_TAG;
        $cacheTags[] = Genmato_ComposerRepo_Model_Customer_Auth::CACHE_TAG . $customerId;

        $packages = false;
        if (Mage::app()->useCache('packages_json')) {
            $packages = json_decode($cache->load(Genmato_ComposerRepo_Model_Customer_Auth::CACHE_TAG . $customerId), true);
        }
        if (!$packages) {
            $config['cached'] = false;
            $joinTable = Mage::getSingleton('core/resource')->getTableName('genmato_composerrepo/customer_packages');
            $collection = Mage::getResourceModel('genmato_composerrepo/packages_collection');
            $collection->getSelect()
                ->joinLeft(
                    array('customer_packages'=> $joinTable),
                    'main_table.entity_id = customer_packages.package_id',
                    array('max_version'=>'last_allowed_version')
                );
            $collection
                ->addFieldToFilter('main_table.status', array('eq' => Genmato_ComposerRepo_Model_Packages::STATUS_ENABLED));
            $collection->addFieldToFilter(
                array(
                    'customer_packages.status',
                    'main_table.bundled_package'
                ),
                array(
                    array('eq'=>Genmato_ComposerRepo_Model_Packages::STATUS_ENABLED),
                    array('eq'=>Genmato_ComposerRepo_Model_Packages::PACKAGE_BUNDLE)
                )
            );

            $collection->addFieldToFilter(
                array(
                    'customer_packages.customer_id',
                    'main_table.bundled_package'
                ),
                array(
                    array('eq'=>$customerId),
                    array('eq'=>Genmato_ComposerRepo_Model_Packages::PACKAGE_BUNDLE)
                )
            );
            $collection->getSelect()->group('main_table.entity_id');

            foreach ($collection as $package) {
                $cacheTags[] = Genmato_ComposerRepo_Model_Packages::CACHE_KEY.$package->getId();

                $packageData = json_decode($package->getPackageJson(), true);
                foreach ($packageData as $version=>$data) {

                    if (!$package->getMaxVersion() ||
                        $data['version_normalized'] == '9999999-dev' ||
                        version_compare($data['version_normalized'], $package->getMaxVersion(), '<=')) {
                        $packages[$package->getName()][$version] = $data;
                    }
                }
            }

            $cache->save(
                json_encode($packages),
                Genmato_ComposerRepo_Model_Customer_Auth::CACHE_TAG . $customerId,
                $cacheTags
            );
        } else {
            $config['cached'] = true;
        }
        $config['packages'] = $packages;
        return $config;
    }
}