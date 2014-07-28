<?php

namespace OroCRM\Bundle\PartnerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PartnerSubscribeCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'orocrm:partner-subscribe';

    protected function configure()
    {
        $this
            ->setName(static::COMMAND_NAME)
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accountsToAdd = array();
        $accountsToRemove = array();
        $configProvider = $this->getContainer()->get('oro_config.global');
        $user = $configProvider->get('oro_crm_partner.github_username');
    }
}
