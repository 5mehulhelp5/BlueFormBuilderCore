<?php
/**
 * Cytracon
 *
 * This source file is subject to the Cytracon Software License, which is available at https://www.cytracon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.cytracon.com for more information.
 *
 * @category  BlueFormBuilder
 * @package   Cytracon_BlueFormBuilderCore
 * @copyright Copyright (C) 2019 Cytracon (https://www.cytracon.com)
 */

namespace Cytracon\BlueFormBuilderCore\Mail\Template;

class TransportBuilder extends \Cytracon\BlueFormBuilderCore\Framework\Mail\Template\TransportBuilder
{
    protected $_body;
    protected $_subject;
    protected $_parts = [];

    /**
     * @var \Magento\Framework\Mail\Message
     */
    protected $message;

    public function setEmailBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    public function getEmailBody()
    {
        return $this->_body;
    }

    public function setEmailSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    public function getEmailSubject()
    {
        return $this->_subject;
    }

    public function getProductMetadata()
    {
        return $this->objectManager->get('Magento\Framework\App\ProductMetadataInterface');
    }

    /**
     * Prepare message
     *
     * @return $this
     */
    protected function prepareMessage()
    {
        if ($this->getProductMetadata()->getVersion() < '2.3.0') {
            $this->message->setMessageType('text/html')
            ->setBody($this->getEmailBody())
            ->setSubject(html_entity_decode($this->getEmailSubject(), ENT_QUOTES));
        } else {
            $this->message->setSubject(html_entity_decode($this->getEmailSubject(), ENT_QUOTES));
            $parts         = $this->getParts();
            $content       = new \Laminas\Mime\Part($this->getEmailBody());
            $content->type = 'text/html';
            $parts[]       = $content;
            $mimeMessage   = new \Laminas\Mime\Message();
            $mimeMessage->setParts($parts);
            $this->message->setBody($mimeMessage);
        }
        return $this;
    }

    public function addAttachment($fileName, $fileContent, $mineType)
    {
        $this->createAttachment(
            $fileContent,
            $mineType,
            \Magento\Framework\HTTP\Mime::DISPOSITION_ATTACHMENT,
            \Magento\Framework\HTTP\Mime::ENCODING_BASE64,
            $fileName
        );

        return $this;
    }

    public function createAttachment(
        $body,
        $mimeType    = \Magento\Framework\HTTP\Mime::TYPE_OCTETSTREAM,
        $disposition = \Magento\Framework\HTTP\Mime::DISPOSITION_ATTACHMENT,
        $encoding    = \Magento\Framework\HTTP\Mime::ENCODING_BASE64,
        $filename    = null
    ) {
        $mp = new \Laminas\Mime\Part($body);
        $mp->encoding    = $encoding;
        $mp->type        = $mimeType;
        $mp->disposition = $disposition;
        $mp->filename    = $filename;

        $this->_addAttachment($mp);

        return $mp;
    }

    /**
     * Adds an existing attachment to the mail message
     *
     * @param  \Laminas\Mime\Part $attachment
     * @return $this
     */
    public function _addAttachment($attachment)
    {
        $this->addPart($attachment);
        return $this;
    }

    public function addPart($part)
    {
        $this->_parts[] = $part;
    }

    public function getParts()
    {
        return $this->_parts;
    }
}
