<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Model_System_Config_Backend_Repository_Enable extends Mage_Core_Model_Config_Data
{

    /**
     * Create Core URL rewrite when enabled or remove if disabled
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $storeId = $this->getScopeId();
            if ($this->getValue()) {
                // New status is enabled => create url rewrite for store
                $rewrite = Mage::getModel('core/url_rewrite')
                    ->setIsSystem(0)
                    ->setStoreId($storeId)
                    ->setDescription('Composer Repository package.json')
                    ->setIdPath($storeId.'/packages.json')
                    ->setRequestPath('packages.json')
                    ->setTargetPath('composer/download/json');
                try {
                    $rewrite->save();
                } catch (Exception $ex) {
                    Mage::log($ex->getMessage());
                }
            } else {
                // New status is disabled => remove url rewrite for store
                $rewrite = Mage::getModel('core/url_rewrite')
                    ->setStoreId($storeId)
                    ->LoadByIdPath($storeId.'/packages.json');

                try {
                    $rewrite->delete();
                } catch (Exception $ex) {
                    Mage::log($ex->getMessage());
                }
            }
        }
    }
}