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

class GitHubAccountSubscribeCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'orocrm:partner-subscribe';
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
            ->setDescription('Runs add or remove collaborators operation')
            ->addOption(
                'usernames-to-add',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'List of usernames which need to be added as collaborators',
                array()
            )->addOption(
                'usernames-to-remove',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'List of usernames which need to be removed from collaborators',
                array()
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $usersToAdd = $input->getOption('usernames-to-add');
        $usersToRemove = $input->getOption('usernames-to-remove');
        $configProvider = $this->getContainer()
            ->get('orocrm_partner.provider.configuration');
        $this->logger = new OutputLogger($output);

        $token = $configProvider->getApiToken();
        $username = $configProvider->getUsername();

        if (empty($token) || empty($username)) {
            $this->logger->error('Token or username is not set');

            return self::STATUS_FAILED;
        }
        $this->getClient()->authenticate($token, null, Client::AUTH_URL_TOKEN);

        $this->logger->notice('Authentication success');

        foreach ($configProvider->getRepositories() as $repository) {
            $this->logger->notice("Repository {$repository} proceed");
            $this->addCollaborators($usersToAdd, $username, $repository);
            $this->removeCollaborators($usersToRemove, $username, $repository);
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
                $this->getCollaborators($this->getClient())->add($username, $repository, $user);
                $this->logger->notice("User {$user} added as collaborator");
            } catch (ExceptionInterface $exception) {
                $this->logger->error("User {$user} not added. Reason: {$exception->getMessage()}");
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
                $this->getCollaborators($this->getClient())->remove($username, $repository, $user);
                $this->logger->notice("User {$user} removed from collaborators");
            } catch (ExceptionInterface $exception) {
                $this->logger->error("User {$user} not removed. Reason: {$exception->getMessage()}");
            }
        }
    }

    /**
     * @param $client
     * @return mixed
     */
    protected function getCollaborators($client)
    {

        if (!$this->collaborators) {
            $this->collaborators = $client->api('repo')
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
