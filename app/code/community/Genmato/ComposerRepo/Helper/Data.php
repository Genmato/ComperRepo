<?php

/**
 * Magento Composer Repository Manager
 *
 * @package Genmato_ComposerRepo
 * @author  Vladimir Kerkhoff <v.kerkhoff@genmato.com>
 * @created 2015-12-09
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */

class Genmato_ComposerRepo_Helper_Data extends Genmato_Core_Helper_Data
{

    /**
     * Send browser response header with optional data to output
     * @param int $responseCode
     * @param string $content
     * @param string $contentType
     * @param bool|false $fileName
     * @return mixed
     */
    protected function sendHeader($responseCode = 400, $content='', $contentType = 'text/html', $fileName = false)
    {
        $action = Mage::app()->getFrontController()->getAction();

        $action->getResponse()
            ->setHttpResponseCode($responseCode)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-Length', strlen($content), true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Last-Modified', date('r'));

        if ($fileName) {
            $action->getResponse()->setHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"', true);
        }

        $action->getResponse()->setBody($content);

        return $action;
    }
}