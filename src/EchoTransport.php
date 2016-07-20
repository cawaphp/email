<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Cawa\Email;

use Swift_DependencyContainer;
use Swift_Events_EventDispatcher;
use Swift_Events_EventListener;
use Swift_Events_SendEvent;
use Swift_Mime_Message;
use Swift_MimePart;
use Swift_Transport;

class EchoTransport implements Swift_Transport
{
    /**
     * @var Swift_Events_EventDispatcher
     */
    private $_eventDispatcher;

    /**
     *
     */
    public function __construct()
    {
        $this->_eventDispatcher = Swift_DependencyContainer::getInstance()->createDependenciesFor('transport.echo')[0];
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return true;
    }

    /**
     * Starts this Transport mechanism.
     */
    public function start()
    {
    }

    /**
     * Stops this Transport mechanism.
     */
    public function stop()
    {
    }

    /**
     * @param Swift_Mime_Message $message
     * @param string[] $failedRecipients An array of failures by-reference
     *
     * @return int The number of sent emails
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        if ($evt = $this->_eventDispatcher->createSendEvent($this, $message)) {
            $this->_eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
            if ($evt->bubbleCancelled()) {
                return 0;
            }
        }

        echo '<div class="cawaEmail">' . "\n";
        echo '<pre class="headers">' . $message->getHeaders()->toString() . '</pre>';
        /** @var Swift_MimePart $minePart */
        foreach($message->getChildren() as $minePart) {
            echo '<div class="parts">' . "\n";
            echo '<pre class="mineHeaders">' . $minePart->getHeaders()->toString() . '</pre>';
            echo '<div class="content">' . "\n";
            echo($minePart->getBody());
            echo '</div>' . "\n";
            echo '</div>' . "\n";
        }
        echo '</div>' . "\n";
        if ($evt) {
            $evt->setResult(Swift_Events_SendEvent::RESULT_SUCCESS);
            $this->_eventDispatcher->dispatchEvent($evt, 'sendPerformed');
        }

        $count = (
            count((array) $message->getTo())
            + count((array) $message->getCc())
            + count((array) $message->getBcc())
            );

        return $count;
    }

    /**
     * Register a plugin.
     *
     * @param Swift_Events_EventListener $plugin
     */
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        $this->_eventDispatcher->bindEventListener($plugin);
    }

    /**
     * Create a new NullTransport instance.
     *
     * @return $this
     */
    public static function newInstance()
    {
        echo <<<EOF
        <style type="text/css">
            .cawaEmail {
                background-color:#fff;
                z-index: 99999;
                position: relative;
                border:1px solid #ccc;
                margin: 10px;
                overflow: hidden;
                word-wrap: break-word;
                box-shadow: 0px 0px 10px grey;
            }
            
            .cawaEmail pre {
                font-family: 'Source Code Pro', Menlo, Monaco, Consolas, monospace;
                color: #333;
                font-size: 12px;
                padding: 5px;
                margin: 0;
                border-bottom:1px solid #ccc;
            }
            
            .cawaEmail pre.headers {
                background-color: LightBlue;
            }
            
            .cawaEmail pre.mineHeaders {
                background-color: LightCyan;
            }
            
            .cawaEmail .content::before,
            .cawaEmail .content::after,
            .cawaEmail .content *::before,
            .cawaEmail .content *::after {
                all: unset;
            }
        </style>
EOF;

        Swift_DependencyContainer::getInstance()
            ->register('transport.echo')
            ->asNewInstanceOf(self::class)
            ->withDependencies(array('transport.eventdispatcher'));

        return new self();
    }
}
