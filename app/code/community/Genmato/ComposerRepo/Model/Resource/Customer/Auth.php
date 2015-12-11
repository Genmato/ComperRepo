<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Model_Resource_Customer_Auth extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('genmato_composerrepo/customer_auth', 'entity_id');
    }

    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCreatedate(now())
                ->setStatus(1);
            $this->generateUniqueAuthKey($object);
        }

        return parent::_beforeSave($object);
    }

    protected function generateUniqueAuthKey(Mage_Core_Model_Abstract $object)
    {
        while ( ($newKey = $this->getUniqueAuthKey()) === false) {
            // Key not unique, try another one
        }

        $object->setAuthKey($newKey)
            ->setAuthSecret(Mage::helper('core')->getRandomString(32));
        return $object;
    }

    protected function getUniqueAuthKey()
    {
        $newKey = Mage::helper('core')->getRandomString(32);

        $select = $this->_getReadAdapter()->select()
            ->from(array('main_table' => $this->getMainTable()))
            ->where('main_table.status = ?', 1)
            ->where('main_table.auth_key = ?', $newKey);

        echo $select;

        if (!$this->_getReadAdapter()->fetchRow($select)) {
            return $newKey;
        }

        return false;
    }
}