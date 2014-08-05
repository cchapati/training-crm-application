<?php

namespace OroCRM\Bundle\PartnerBundle\Model;

use Github\Api\Organization\Teams;
use Github\Client;
use Github\Exception\ExceptionInterface;

use OroCRM\Bundle\PartnerBundle\Exception\InvalidConfigurationException;
use OroCRM\Bundle\PartnerBundle\Exception\InvalidResponseException;
use OroCRM\Bundle\PartnerBundle\Provider\ConfigurationProvider;

class GitHubCollaboratorManager
{
    /**
     * @var ConfigurationProvider
     */
    protected $configProvider;

    /**
     * @var GitHubClientFactory
     */
    protected $gitHubClientFactory;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $teams = array();

    /**
     * @param ConfigurationProvider $configurationProvider
     * @param GitHubClientFactory   $gitHubClientFactory
     */
    public function __construct(ConfigurationProvider $configurationProvider, GitHubClientFactory $gitHubClientFactory)
    {
        $this->configProvider = $configurationProvider;
        $this->gitHubClientFactory = $gitHubClientFactory;
    }

    /**
     * @param string $username
     * @throws InvalidResponseException
     */
    public function addCollaborator($username)
    {
        foreach ($this->configProvider->getTeams() as $team) {
            try {
                $this->getTeamsApi()->addMember($this->getTeamId($team), $username);
            } catch (ExceptionInterface $e) {
                $message = "Can't add user \"{$username}\" to GitHub team \"{$team}\".";
                throw InvalidResponseException::create($message, $e);
            }
        }
    }

    /**
     * @param string $username
     * @throws InvalidResponseException
     */
    public function removeCollaborator($username)
    {
        foreach ($this->configProvider->getTeams() as $team) {
            try {
                $id = $this->getTeamId($team);
                $this->getTeamsApi()->removeMember($id, $username);
            } catch (ExceptionInterface $e) {
                $message = "Can't remove user \"{$username}\" from GitHub team \"{$team}\".";
                throw InvalidResponseException::create($message, $e);
            }
        }
    }

    /**
     * @return Client
     * @throws InvalidConfigurationException
     */
    protected function getClient()
    {
        if (!$this->client) {
            $this->client = $this->gitHubClientFactory->createClient();

            $token = $this->configProvider->getApiToken();
            if (empty($token)) {
                throw new InvalidConfigurationException('GitHub API token isn\'t set.');
            }
            $this->client->authenticate($token, null, Client::AUTH_URL_TOKEN);
        }

        return $this->client;
    }

    protected function getTeamId($name)
    {
        $organization = $this->configProvider->getOrganization();
        if (!$this->teams) {
            $teamsApi = $this->getTeamsApi();
            $teams = $teamsApi->all($organization);
            if (is_array($teams)) {
                foreach ($teams as $team) {
                    $this->teams[$team['name']] = $team['id'];
                }
            }
        }

        if (!isset($this->teams[$name])) {
            throw new InvalidConfigurationException(
                "GitHub team \"{$name}\" not exist in organization \"{$organization}\"."
            );
        }

        return $this->teams[$name];
    }

    /**
     * @return Teams
     */
    protected function getTeamsApi()
    {
        /**
         * @var Teams $teamsApi
         */
        $teamsApi = $this->getClient()
            ->api('teams');
        return $teamsApi;
    }
}
