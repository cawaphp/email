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

namespace Cawa\Email\Commands;

use Cawa\Console\Command;
use Cawa\Console\ConsoleOutput;
use Cawa\Core\DI;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Message extends Command
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('email:message')
            ->setDescription('Send an generated email')
            ->addArgument('subject', InputArgument::REQUIRED, 'Subject')
            ->addArgument('body', InputArgument::REQUIRED, 'Html body')
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'Destination email name')
            ->addOption('reply-to', null, InputOption::VALUE_OPTIONAL, 'Reply to email')
            ->addOption('cc', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'CC email')
            ->addOption('bcc', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'BCC email')
            ->addOption('headers', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Headers')
            ->addArgument('email', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Destination email')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface|ConsoleOutput $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $message = new \Cawa\Email\Message();

        $message->setTo(
                $input->getArgument('email'),
                $input->getOption('name') && is_string($input->getOption('name')) ?
                    $input->getOption('name') :
                    null
            )
            ->setFrom(DI::config()->get('email/fromEmail'), DI::config()->get('email/fromName'))
            ->setSubject($input->getArgument('subject'))
            ->setHtmlBody($input->getArgument('body'));

        if ($input->getOption('reply-to') && is_string($input->getOption('reply-to'))) {
            $message->setReplyTo($input->getOption('reply-to'));
        }

        if ($input->getOption('cc')) {
            $message->setCc($input->getOption('cc'));
        }

        if ($input->getOption('bcc')) {
            $message->setBcc($input->getOption('bcc'));
        }

        if ($input->getOption('headers')) {
            foreach ($input->getOption('headers') as $key => $value) {
                $message->getHeaders()->addTextHeader($key, $value);
            }
        }

        $message->send();

        $output->writeln(sprintf(
            "[%s] Send to '%s' with subject '%s' in %s s",
            get_class(),
            is_array($input->getArgument('email')) ? implode(', ', $input->getArgument('email')) : $input->getArgument('email'),
            $input->getArgument('subject'),
            $this->getDuration()
        ));

        return 0;
    }
}
