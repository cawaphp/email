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

namespace Cawa\Email\Plugins;

use Swift_Events_SendEvent;
use Swift_Events_SendListener;

class Disconnect implements Swift_Events_SendListener
{
    /**
     * {@inheritdoc}
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
        $evt->getTransport()->stop();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {

    }
}
