<?php

class Genmato_ComposerRepo_Helper_Notify extends Genmato_ComposerRepo_Helper_Data
{
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
                ->setCreatedate(now())
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