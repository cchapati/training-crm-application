<?php

namespace OroCRM\Bundle\PartnerBundle\Model;

use Github\Api\Repository\Collaborators;
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
     * @var Client
     */
    protected $client;

    /**
     * @param ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configProvider = $configurationProvider;
    }

    /**
     * @param string $username
     * @throws InvalidResponseException
     */
    public function addCollaborator($username)
    {
        $this->authenticate();
        foreach ($this->configProvider->getRepositories() as $repository) {
            if (empty($repository['owner']) || empty($repository['name'])) {
                continue;
            }
            try {
                $this->getCollaborators()->add($repository['owner'], $repository['name'], $username);
            } catch (ExceptionInterface $e) {
                $message = "Can't add Collaborator({$username}) to({$repository['owner']}/{$repository['name']}).";
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
        $this->authenticate();
        foreach ($this->configProvider->getRepositories() as $repository) {
            if (empty($repository['owner']) || empty($repository['name'])) {
                continue;
            }

            try {
                $this->getCollaborators()->remove($repository['owner'], $repository['name'], $username);
            } catch (ExceptionInterface $e) {
                $message = "Can't remove Collaborator({$username}) from({$repository['owner']}/{$repository['name']}).";
                throw InvalidResponseException::create($message, $e);
            }
        }
    }

    /**
     * @throws InvalidConfigurationException
     */
    protected function authenticate()
    {
        $token = $this->configProvider->getApiToken();

        if (empty($token)) {
            throw new InvalidConfigurationException('Token is not set');
        }

        $this->getClient()->authenticate($token, null, Client::AUTH_URL_TOKEN);
    }

    /**
     * @return Collaborators
     */
    protected function getCollaborators()
    {
        return $this->getClient()->api('repo')
            ->collaborators();
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
