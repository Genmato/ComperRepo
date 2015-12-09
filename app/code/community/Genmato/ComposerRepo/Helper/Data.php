<?php

class Genmato_ComposerRepo_Helper_Data extends Genmato_Core_Helper_Data
{

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