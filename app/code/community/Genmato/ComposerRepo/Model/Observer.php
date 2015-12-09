<?php

class Genmato_ComposerRepo_Model_Observer
{
    /**
     * Create customer_packages records for ordered/invoiced products
     * E-mail instructions on how to install package thru composer
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesOrderInvoicePay(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $event->getInvoice();
        /** @var Mage_Sales_Model_Order $order */
        $order = $invoice->getOrder();
        $customerId = $order->getCustomerId();

        $packageModel = Mage::getModel('genmato_composerrepo/packages');

        $installPackages = [];
        foreach($invoice->getItemsCollection() as $item) {
            $package = $packageModel->load($item->getProductId(), 'product_id');

            if ($package->getId()) {
                $installPackages[] = $package->getName();

                $customerPackage = Mage::getModel('genmato_composerrepo/customer_packages')
                    ->setCreatedate(now())
                    ->setStatus(1)
                    ->setCustomerId($customerId)
                    ->setOrderId($order->getId())
                    ->setPackageId($package->getId())
                    ->setLastAllowedVersion($package->getVersion());

                if ($period = Mage::getStoreConfig('genmato_composerrepo/configuration/update_period')) {
                    $endDate = new DateTime();
                    $endDate->add(new DateInterval('P'.intval($period).'M'));

                    $customerPackage->setLastAllowedDate($endDate->format('Y-m-d H:i:s'));
                }

                try {
                    $customerPackage->save();
                } catch (Exception $ex) {
                    Mage::log($ex->getMessage());
                }
            }
        }

        $customerKeys = Mage::getModel('genmato_composerrepo/customer_auth')
            ->getCollection()
            ->addFieldToFilter('status', array('eq'=>1))
            ->addFieldToFilter('default', array('eq'=>1))
            ->addFieldToFilter('customer_id', array('eq'=>$customerId))
            ->getFirstItem();

        if (!$customerKeys->getId()) {
            $customerKeys
                ->setCreatedate(now())
                ->setStatus(1)
                ->setDefault(1)
                ->setCustomerId($customerId)
                ->setDescription('Auto generated keys')
                ->setAuthKey(Mage::helper('core')->getRandomString(32))
                ->setAuthSecret(Mage::helper('core')->getRandomString(32));
            try {
                $customerKeys->save();
            } catch (Exception $ex) {
                Mage::log($ex->getMessage());
            }
        }
        $cleanTags = [];
        $cleanTags[] = Genmato_ComposerRepo_Model_Customer_Auth::CACHE_TAG.$customerId;
        Mage::app()->getCache()->clean('matchingTag', $cleanTags);

        $customerKeys->sendEmail($installPackages);
    }

    /**
     * Disable package access when order is refunded
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesOrderCreditmemoSaveCommitAfter(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var Mage_Sales_Model_Order_Creditmemo $creditmemo */
        $creditmemo = $event->getCreditmemo();
        /** @var Mage_Sales_Model_Order $order */
        $order = $creditmemo->getOrder();
        $customerId = $order->getCustomerId();

        $productIds = [];
        foreach ($creditmemo->getItemsCollection() as $item) {
            $productIds[] = $item->getProductId();
        }

        $packageIds = Mage::getResourceModel('genmato_composerrepo/packages_collection')
            ->addFielToFilter('product_id', array('in'=>$productIds))
            ->getAllIds();

        $collection = Mage::getResourceModel('genmato_composerrepo/customer_packages_collection')
            ->addFieldToFilter('status', array('eq'=>1))
            ->addFieldToFilter('customer_id', array('eq'=>$customerId))
            ->addFieldToFilter('order_id', array('eq'=>$order->getId()))
            ->addFieldToFilter('package_id', array('in'=>$packageIds));

        foreach ($collection as $item) {
            $item->setStatus(0);
        }
        try {
            $collection->save();
        } catch (Exception $ex) {
            Mage::log($ex->getMessage());
        }
        $cleanTags = [];
        $cleanTags[] = Genmato_ComposerRepo_Model_Customer_Auth::CACHE_TAG.$customerId;
        Mage::app()->getCache()->clean('matchingTag', $cleanTags);
    }
}