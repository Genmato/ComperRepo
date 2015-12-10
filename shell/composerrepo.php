<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

require_once 'abstract.php';

class Genmato_Shell_Composer_Repo extends Mage_Shell_Abstract
{

    protected $store_id=0;
    /**
     * Run script
     *
     */
    public function run()
    {
        if ($this->getArg('store_id')) {
            $this->store_id = $this->getArg('store_id');
        }

        if (isset($this->_args['update'])) {
            $config =[];
            $config['name'] = $this->getConfig('name');
            $config['homepage'] = $this->getConfig('homepage');
            $config['output-dir'] = $this->getConfig('output-dir');
            $config['notify-batch'] = $this->getUrl('composer/download/notify');

            $config['repositories'] = $this->getRepositories();
            $config['archive'] = [];
            $config['archive']['directory'] = 'file';
            $config['archive']['format'] = $this->getConfig('format', 'satis_archive');
            $config['archive']['prefix-url'] = substr($this->getUrl('composer/download'),0,-1);
            $config['archive']['absolute-directory'] = $this->getConfig('absolute-directory', 'satis_archive');
            $config['archive']['checksum'] = false;
            $config['archive']['skip-dev'] = false;
            $config['require-all'] = true;


            $satisBin = $this->getConfig('bin');
            $satisCfg = $this->getConfig('config');

            if ($satisBin && $satisCfg) {
                file_put_contents(
                    $satisCfg,
                    $this->json($config)
                );

                $execute = $satisBin.' -vvv build '.$satisCfg;
                system($execute);

                $packages = json_decode(file_get_contents($this->getConfig('output-dir').DS.'packages.json'), true);

                $cleanTags = [];
                $currentDateTime = Mage::getModel('core/date')->gmtDate();

                foreach ($packages['includes'] as $file=>$data) {
                    $includeData = json_decode(file_get_contents($this->getConfig('output-dir').DS.$file), true);

                    foreach ($includeData['packages'] as $packageName=>$packageData) {
                        $packageModel = Mage::getModel('genmato_composerrepo/packages')->getByPackageName($packageName);

                        if (!$packageModel->getId()) {
                            continue;
                        }
                        $this->printLn('Building data for package: '. $packageModel->getName());

                        $versions = [];
                        $latestVersion = '0.0.0.0';
                        $updatePackageData = false;
                        if (!$this->getConfig('include_dev','configuration') && isset($packageData['dev-master'])) {
                            $this->printLn(' - Removing dev-master from available packages');
                            unset($packageData['dev-master']);
                        }
                        foreach ($packageData as $version => $versionInfo) {
                            unset($versionInfo['source']);
                            if (isset($versionInfo['support']['source'])) {
                                unset($versionInfo['support']['source']);
                            }
                            if (isset($versionInfo['support']['issues'])) {
                                unset($versionInfo['support']['issues']);
                            }

                            $versionNr = $versionInfo['version_normalized'];
                            $filePart = explode(DS, $versionInfo['dist']['url']);

                            $versionModel = Mage::getModel('genmato_composerrepo/packages_versions')
                                ->getByPackageVersion($packageModel->getId(), $versionNr);
                            if (!$versionModel->getId()) {
                                $versionModel->setPackageId($packageModel->getId())
                                    ->setFile(array_pop($filePart))
                                    ->setVersion($versionNr);
                                $versionModel->save();
                                $updatePackageData = true;
                                $this->printLn(' - Saving new version: '.$versionNr);
                            }
                            if (strstr($versionModel->getFile(),$versionInfo['dist']['reference']) === false) {
                                $versionModel->setFile($filePart)
                                    ->setVersion($versionNr)
                                    ->save();
                                $updatePackageData = true;
                                $this->printLn(' - Saving updated version reference: '.$versionNr);
                            }

                            $param = [];
                            $param['m'] = str_replace('/','_',$packageName);
                            $param['h'] = $versionInfo['dist']['reference'];
                            $param['v'] = $versionNr;
                            $versionInfo['dist']['url'] = $this->getUrl('composer/download/file', $param);

                            $versions[$version] = $versionInfo;
                            if ($versionNr != '9999999-dev' && version_compare($versionNr, $latestVersion, '>')) {
                                $latestVersion = $versionNr;
                            }
                        }

                        if ($packageModel->getVersion() != $latestVersion) {
                            $packageModel->setVersion($latestVersion);
                            $updatePackageData = true;

                            $custPackages = Mage::getResourceModel('genmato_composerrepo/customer_packages_collection')
                                ->addFieldToFilter('package_id', array('eq'=>$packageModel->getId()))
                                ->addFieldToFilter('status', array('eq'=>1))
                                ->addFieldToFilter('last_allowed_date', array('null'=>true,'from'=>$currentDateTime));
                            $this->printLn(' - Updating max allowed version for customers');
                            foreach ($custPackages as $custPackage) {
                                $this->printLn('   - '.$custPackage->getCustomerId());
                                $custPackage->setLastAllowedVersion($latestVersion);
                                $cleanTags[] = Genmato_ComposerRepo_Model_Customer_Auth::CACHE_TAG.$custPackage->getCustomerId();
                            }
                            $custPackages->save();

                        }
                        if ($updatePackageData) {
                            $packageModel->setPackageJson(json_encode($versions))
                                ->save();
                            $cleanTags[] = Genmato_ComposerRepo_Model_Packages::CACHE_KEY.$packageModel->getId();
                        }
                    }
                }
                if (count($cleanTags)>0) {
                    Mage::app()->getCache()->clean('matchingTag', $cleanTags);
                }
            }
        } else {
            echo $this->usageHelp();
        }
    }

    protected function json($array)
    {
        return json_encode($array, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }

    protected function getRepositories()
    {
        $result = [];
        $collection = Mage::getResourceModel('genmato_composerrepo/packages_collection')
            ->addFieldToFilter('status', array('neq' => 0));
        foreach ($collection as $package) {
            $item = json_decode($package->getRepositoryOptions(), true);
            if (!isset($item['type'])) {
                $item['type'] = 'vcs';
            }
            $item['url'] = $package->getRepositoryUrl();
            $result[] = $item;
        }
        return $result;
    }

    protected function getConfig($node = '', $path = 'satis', $main = 'genmato_composerrepo')
    {
        return Mage::getStoreConfig($main.'/'.$path.'/'.$node, $this->store_id);
    }

    protected function getUrl($route, $params = [])
    {
        $url = Mage::app()->getStore($this->store_id)->getBaseUrl().$route;
        foreach ($params as $key=>$val) {
            $url .= "/$key/$val";
        }
        return $url;
    }

    protected function printLn($msg)
    {
        echo $msg.PHP_EOL;
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f composerrepo.php -- [options]

  update        Update
  help          This help

USAGE;
    }
}

$shell = new Genmato_Shell_Composer_Repo();
$shell->run();
