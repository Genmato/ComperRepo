<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Helper_Download extends Genmato_ComposerRepo_Helper_Data
{
    /**
     * Check if access to (and output) requested download file is allowed
     * @param $customerId
     * @param $params
     * @return mixed
     */
    public function getFile($customerId, $params)
    {
        $packageName = isset($params['m']) ? str_replace('_','/',$params['m']) : '';
        $packageVersion = isset($params['v']) ? $params['v'] : '';
        $packageHash = isset($params['h']) ? $params['h'] : '';

        $package = Mage::getModel('genmato_composerrepo/packages')->getByPackageName($packageName);

        if (!$package->getId() || $package->getStatus()==Genmato_ComposerRepo_Model_Packages::STATUS_DISABLED) {
            return $this->setHeaderUnknown($this->__('Unknown Package'));
        }

        $packageId = $package->getId();
        if ($package->getStatus() == Genmato_ComposerRepo_Model_Packages::STATUS_ENABLED) {
            $customerPackge = Mage::getModel('genmato_composerrepo/customer_packages')
                ->getCollection()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('package_id', $packageId)
                ->addFieldToFilter('customer_id', $customerId)
                ->getFirstItem();

            if (!$customerPackge->getId()) {
                return $this->setHeaderUnknown($this->__('Unauthorized Package Name'));
            }

            if (version_compare($packageVersion, $customerPackge->getLastAllowedVersion(), '>')) {
                return $this->setHeaderUnknown($this->__('Unauthorized Package Version'));
            }

        }
        $version = Mage::getModel('genmato_composerrepo/packages_versions')
            ->getByPackageVersion($packageId, $packageVersion);

        if (!$version->getId()) {
            return $this->setHeaderUnknown($this->__('Unknown Package Version'));
        }

        $file = $version->getFile();

        if(strstr($file, $packageHash) === false) {
            return $this->setHeaderUnknown($this->__('Invalid Package Version Hash'));
        }

        $packageDir = $this->getConfig('absolute-directory', 'satis_archive');

        return $this->sendFile($packageDir.DS.$file, $packageName, $packageVersion);
    }

    /**
     * Output 404 error message (file not found) with message
     *
     * @param $message
     * @return mixed
     */
    protected function setHeaderUnknown($message)
    {
        return $this->sendHeader(404, $message);
    }

    /**
     * Send file to download
     *
     * @param $fileName
     * @param $packageName
     * @param $packageVersion
     * @return mixed
     */
    protected function sendFile($fileName, $packageName, $packageVersion)
    {
        if(!file_exists($fileName)) {
            return $this->setHeaderUnknown($this->__('File not Found'));
        }

        $content = file_get_contents($fileName);
        $type = $this->getConfig('format', 'satis_archive');
        $outputName = $packageName.'-'.$packageVersion.'.'.$type;

        $this->sendHeader(200, $content, 'application/octet-stream', $outputName);
    }

    /**
     * Get Config parameter
     *
     * @param string $node
     * @param string $path
     * @param string $main
     * @return mixed
     */
    protected function getConfig($node = '', $path = 'satis', $main = 'genmato_composerrepo')
    {
        return Mage::getStoreConfig($main.'/'.$path.'/'.$node);
    }
}