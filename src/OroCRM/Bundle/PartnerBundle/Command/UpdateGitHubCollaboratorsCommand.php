<?php

namespace OroCRM\Bundle\PartnerBundle\Command;

use Github\Api\Repository\Collaborators;
use Github\Client;
use Github\Exception\ExceptionInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Oro\Component\Log\OutputLogger;

class UpdateGitHubCollaboratorsCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'orocrm:partner:update-github-collaborators';
    const STATUS_SUCCESS = 0;
    const STATUS_FAILED  = 255;

    /**
     * @var Collaborators
     */
    protected $collaborators;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var OutputLogger
     */
    protected $logger;

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Adds and/or removes collaborators to GihHub repositories for partners.')
            ->addOption(
                'add-users',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'List of GitHub usernames to add to collaborators',
                array()
            )->addOption(
                'remove-users',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'List of GitHub usernames to remove from collaborators',
                array()
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $addUsers = $input->getOption('add-users');
        $removeUsers = $input->getOption('remove-users');
        $configProvider = $this->getContainer()
            ->get('orocrm_partner.provider.configuration');
        $this->logger = new OutputLogger($output);

        $token = $configProvider->getApiToken();

        if (empty($token)) {
            $this->logger->error('Token is not set', array('exception' => null));

            return self::STATUS_FAILED;
        }
        $this->getClient()->authenticate($token, null, Client::AUTH_URL_TOKEN);

        $this->logger->notice('Authentication success');

        foreach ($configProvider->getRepositories() as $repository) {
            $this->logger->notice("Repository {$repository['origin']} proceed");

            if (empty($repository['owner']) || empty($repository['name'])) {
                $this->logger->warning("Incorrect repository {$repository['origin']} format. Will be skipped");
                continue;
            }

            $this->addCollaborators($addUsers, $repository['owner'], $repository['name']);
            $this->removeCollaborators($removeUsers, $repository['owner'], $repository['name']);
        }

        return self::STATUS_SUCCESS;
    }

    /**
     * @param $usersToAdd
     * @param $username
     * @param $repository
     */
    protected function addCollaborators($usersToAdd, $username, $repository)
    {
        foreach ($usersToAdd as $user) {
            try {
                $this->getCollaborators()->add($username, $repository, $user);
                $this->logger->notice("User {$user} added as collaborator");
            } catch (ExceptionInterface $exception) {
                $this->logger->error(
                    "User {$user} not added. Reason: {$exception->getMessage()}",
                    array('exception' => $exception)
                );
            }
        }
    }

    /**
     * @param $usersToRemove
     * @param $username
     * @param $repository
     */
    protected function removeCollaborators($usersToRemove, $username, $repository)
    {
        foreach ($usersToRemove as $user) {
            try {
                $this->getCollaborators()->remove($username, $repository, $user);
                $this->logger->notice("User {$user} removed from collaborators");
            } catch (ExceptionInterface $exception) {
                $this->logger->error(
                    "User {$user} not removed. Reason: {$exception->getMessage()}",
                    array('exception' => $exception)
                );
            }
        }
    }

    /**
     * @return Collaborators
     */
    protected function getCollaborators()
    {

        if (!$this->collaborators) {
            $this->collaborators = $this->getClient()->api('repo')
                ->collaborators();
        }

        return $this->collaborators;
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        if (!$this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }
}
