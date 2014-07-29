<?php

namespace OroCRM\Bundle\PartnerBundle\Provider;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;

class ConfigurationProvider
{
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

        $repositoriesArray = array();
        foreach (preg_split("/\r\n|\n|\r/", $repositories) as $repository) {
            $repository = trim($repository);
            $repository = preg_replace('/http[s]?:\/\/github.com\//', '', $repository);
            $repositoryParts = explode('/', $repository, 2);

            $repositoriesArray[] = array(
                'owner'  => $repositoryParts[0],
                'name'   => empty($repositoryParts[1]) ? '' : $repositoryParts[1],
                'origin' => $repository
            );
        }

        return $repositoriesArray;
    }
}
