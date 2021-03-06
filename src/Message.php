<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Cawa\Email;

/**
 * @method addPart
 */
class Message
{
    use MailerFactory;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Swift_Message
     */
    private $message;

    /**
     * @var Listener
     */
    private static $listener;

    /**
     * Create a new Message.
     *
     * @param string $name
     */
    public function __construct(string $name = null)
    {
        $this->mailer = self::mailer($name);

        if (!self::$listener) {
            self::$listener = new Listener();
            $this->mailer->getTransport()->registerPlugin(self::$listener);
        }

        $this->message = new \Swift_Message();
    }

    /**
     * @return string|null
     */
    public function getHtmlBody()
    {
        foreach ($this->message->getChildren() as $child) {
            if ($child->getContentType() == 'text/html') {
                return $child->getBody();
            }
        }

        return null;
    }

    /**
     * @param string $html
     *
     * @return $this|self
     */
    public function setHtmlBody(string $html) : self
    {
        $this->message->addPart($html, 'text/html');

        return $this;
    }

    /**
     * @throws Exception
     *
     * @return bool
     */
    public function send() : bool
    {
        if (!$this->getTo() && !$this->getCc() && !$this->getBcc()) {
            throw new Exception('Cannot send message without a destination address');
        }

        $return = $this->mailer->send($this->message) > 0 ? true : false;
        self::$listener->sendLog();

        return $return;
    }

    /**
     * @return string
     */
    public function toString() : string
    {
        return $this->message->toString();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function attachSigner(\Swift_Signer $signer) : self
    {
        $this->message->attachSigner($signer);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function detachSigner(\Swift_Signer $signer) : self
    {
        $this->message->detachSigner($signer);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setBody($body, $contentType = null, $charset = null) : self
    {
        $this->message->setBody($body, $contentType, $charset);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCharset()
    {
        return $this->message->getCharset();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setCharset($charset) : self
    {
        $this->message->setCharset($charset);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return $this->message->getFormat();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setFormat($format) : self
    {
        $this->message->setFormat($format);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDelSp()
    {
        return $this->message->getDelSp();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setDelSp($delsp = true) : self
    {
        $this->message->setDelSp($delsp);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function charsetChanged($charset)
    {
        $this->message->charsetChanged($charset);
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setSubject($subject) : self
    {
        $this->message->setSubject($subject);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->message->getSubject();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setDate($date) : self
    {
        $this->message->setDate($date);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDate()
    {
        return $this->message->getDate();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setReturnPath($address) : self
    {
        $this->message->setReturnPath($address);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnPath()
    {
        return $this->message->getReturnPath();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setSender($address, $name = null) : self
    {
        $this->message->setSender($address, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSender()
    {
        return $this->message->getSender();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function addFrom($address, $name = null) : self
    {
        $this->message->addFrom($address, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setFrom($addresses, $name = null) : self
    {
        $this->message->setFrom($addresses, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrom()
    {
        return $this->message->getFrom();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function addReplyTo($address, $name = null) : self
    {
        $this->message->addReplyTo($address, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setReplyTo($addresses, $name = null) : self
    {
        $this->message->setReplyTo($addresses, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReplyTo()
    {
        return $this->message->getReplyTo();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function addTo($address, $name = null) : self
    {
        $this->message->addTo($address, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setTo($addresses, $name = null) : self
    {
        $this->message->setTo($addresses, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTo()
    {
        return $this->message->getTo();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function addCc($address, $name = null) : self
    {
        $this->message->addCc($address, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setCc($addresses, $name = null) : self
    {
        $this->message->setCc($addresses, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCc()
    {
        return $this->message->getCc();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function addBcc($address, $name = null) : self
    {
        $this->message->addBcc($address, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setBcc($addresses, $name = null) : self
    {
        $this->message->setBcc($addresses, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBcc()
    {
        return $this->message->getBcc();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setPriority($priority) : self
    {
        $this->message->setPriority($priority);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->message->getPriority();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setReadReceiptTo($addresses) : self
    {
        $this->message->setReadReceiptTo($addresses);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReadReceiptTo()
    {
        return $this->message->getReadReceiptTo();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function attach(\Swift_Mime_SimpleMimeEntity $entity) : self
    {
        $this->message->attach($entity);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function detach(\Swift_Mime_SimpleMimeEntity $entity) : self
    {
        $this->message->detach($entity);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function embed(\Swift_Mime_SimpleMimeEntity $entity)
    {
        return $this->message->embed($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function generateId()
    {
        return $this->message->generateId();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->message->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->message->getContentType();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setContentType($type) : self
    {
        $this->message->setContentType($type);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->message->getId();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setId($id) : self
    {
        $this->message->setId($id);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->message->getDescription();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setDescription($description) : self
    {
        $this->message->setDescription($description);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxLineLength()
    {
        return $this->message->getMaxLineLength();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setMaxLineLength($length) : self
    {
        $this->message->setMaxLineLength($length);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->message->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function getEncoder()
    {
        return $this->message->getEncoder();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setEncoder(\Swift_Mime_ContentEncoder $encoder) : self
    {
        $this->message->setEncoder($encoder);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBoundary()
    {
        return $this->message->getBoundary();
    }

    /**
     * {@inheritdoc}
     *
     * @return $this|self
     */
    public function setBoundary($boundary) : self
    {
        $this->message->setBoundary($boundary);

        return $this;
    }
}
