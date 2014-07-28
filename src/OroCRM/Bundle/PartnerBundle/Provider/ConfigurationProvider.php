<?php

namespace OroCRM\Bundle\PartnerBundle\Provider;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;

class ConfigurationProvider
{
    const USERNAME_FIELD = 'oro_crm_partner.github_username';
    const TOKEN_FIELD = 'oro_crm_partner.github_api_token';
    const REPOSITORIES_FIELD = 'oro_crm_partner.github_repositories';

    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @param ConfigManager $configManager
     */
    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->configManager->get(self::USERNAME_FIELD);
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->configManager->get(self::TOKEN_FIELD);
    }

    /**
     * @return array
     */
    public function getRepositories()
    {
        $repositories = $this->configManager->get(self::REPOSITORIES_FIELD);

        if (empty($repositories)) {
            return array();
        }

        $repositoriesArray = explode(',', $repositories);
        foreach ($repositoriesArray as &$repository) {
            $repository = trim($repository);
        }

        return $repositoriesArray;
    }
}
