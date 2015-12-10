<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Helper_Notify extends Genmato_ComposerRepo_Helper_Data
{
    /**
     * Store composer installation notification
     * @param $downloadPackages
     * @param $customer
     */
    public function storeNotifyData($downloadPackages, $customer)
    {
        $packageModel = Mage::getModel('genmato_composerrepo/packages');
        $versionModel = Mage::getModel('genmato_composerrepo/packages_versions');

        $remoteAddr = Mage::helper('core/http')->getRemoteAddr(false);
        foreach ($downloadPackages as $pDownload) {
            $package = $packageModel->getByPackageName($pDownload['name']);
            if (!$package->getId()) {
                continue;
            }

            $version = $versionModel->getByPackageVersion($package->getId(), $pDownload['version']);
            if (!$version->getId()) {
                continue;
            }

            $notify = Mage::getModel('genmato_composerrepo/packages_notify')
                ->setCustomerId($customer->getCustomerId())
                ->setCustomerAuthId($customer->getId())
                ->setPackageId($package->getId())
                ->setVersionId($version->getId())
                ->setRemoteIp($remoteAddr);

            try {
                $notify->save();
            } catch (Exception $ex) {
                Mage::log($ex->getMessage());
            }
        }
    }
}