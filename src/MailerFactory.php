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

use Cawa\Core\DI;
use Cawa\Net\Uri;

trait MailerFactory
{
    /**
     * @param string $name
     *
     * @return \Swift_Mailer
     */
    private static function mailer(string $name = null) : \Swift_Mailer
    {
        if ($return = DI::get(__METHOD__)) {
            return $return;
        }

        $config = DI::config()->getIfExists('email/' . ($name ?: 'default'));
        if (!$config) {
            $transport = \Swift_MailTransport::newInstance();
            $return = \Swift_Mailer::newInstance($transport);
        } elseif (is_callable($config)) {
            $return = $config();
        } else {
            $uri = new Uri($config);
            switch ($uri->getScheme()) {
                case 'smtp':
                    $transport = \Swift_SmtpTransport::newInstance($uri->getHost(), $uri->getPort());

                    if ($uri->getUser()) {
                        $transport->setUsername($uri->getUser());
                    }

                    if ($uri->getPassword()) {
                        $transport->setPassword($uri->getPassword());
                    }

                    if ($uri->getQuery('auth')) {
                        $transport->setAuthMode($uri->getQuery('auth'));
                    }

                    if ($uri->getQuery('encryption')) {
                        $transport->setEncryption($uri->getQuery('encryption'));
                    }

                    break;
                case 'echo':
                    $transport = EchoTransport::newInstance();
                    break;

                default:
                    throw new \InvalidArgumentException(sprintf("Undefined email mailer type '%s'", $uri->getScheme()));
                    break;
            }

            $return = \Swift_Mailer::newInstance($transport);

            if ($uri->getQuery('plugins')) {
                foreach ($uri->getQuery('plugins') as $plugin)
                $return->registerPlugin(new $plugin);
            }
        }

        return DI::set(__METHOD__, $name, $return);
    }
}
