<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Model_Customer_Auth extends Mage_Core_Model_Abstract
{

    const CACHE_KEY = 'composer_repo_customer_key_';
    const CACHE_TAG = 'composer_repo_customer_';
    /**
     * Identifier for history item
     */
    const ENTITY              = 'composerrepo';

    /**
     * Event type names for order emails
     */
    const EMAIL_EVENT_NAME    = 'installation_details';

    protected function _construct()
    {
        $this->_init('genmato_composerrepo/customer_auth');
    }

    /**
     * Email installation instructions e-mail to customer
     *
     * @param $packages
     * @return bool|void
     * @throws Mage_Core_Exception
     */
    public function sendEmail($packages)
    {
        $appEmulation = Mage::getSingleton('core/app_emulation');

        if (!Mage::getStoreConfigFlag('genmato_composerrepo/configuration/email_active')) {
            return;
        }
        $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());

        $store = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore();
        $storeId = $store->getId();
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        $mailTemplate = Mage::getStoreConfig('genmato_composerrepo/configuration/email_template', $storeId);
        $mailIdentity = Mage::getStoreConfig('genmato_composerrepo/configuration/email_identity', $storeId);
        $mailBcc = explode(',', Mage::getStoreConfig('genmato_composerrepo/configuration/email_bcc', $storeId));
        $copyMethod = 'bcc';

        $filter = array(
            'store' => $store,
            'auth_key' => $this->getAuthKey(),
            'auth_secret' => $this->getAuthSecret(),
            'repo_id' => Mage::getStoreConfig('genmato_composerrepo/configuration/repo_id'),
            'repo_url' => Mage::getStoreConfig('genmato_composerrepo/configuration/repo_url'),
            'customer' => $customer,
            'packages' => $packages,
        );
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        /** @var $emailInfo Mage_Core_Model_Email_Info */
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($customer->getEmail(), $customer->getFirstname() . ' ' . $customer->getLastname());
        if ($mailBcc && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($mailBcc as $email) {
                if (!empty($email)) {
                    $emailInfo->addBcc($email);
                }
            }
        }
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender($mailIdentity);
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($mailTemplate);
        $mailer->setTemplateParams($filter);

        /** @var $emailQueue Mage_Core_Model_Email_Queue */
        $emailQueue = Mage::getModel('core/email_queue');
        $emailQueue->setEntityId($this->getId())
            ->setEntityType(self::ENTITY)
            ->setEventType(self::EMAIL_EVENT_NAME)
            ->setIsForceCheck(true);

        $mailer->setQueue($emailQueue)->send();

        return true;
    }
}