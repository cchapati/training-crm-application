<?php

namespace OroCRM\Bundle\PartnerBundle\Provider;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;

class ConfigurationProvider
{
    const TOKEN_FIELD = 'oro_crm_partner.github_api_token';
    const ORGANIZATION_FIELD = 'oro_crm_partner.github_organization';
    const TEAMS_FIELD = 'oro_crm_partner.github_teams';

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
    public function getTeams()
    {
        $teams = $this->configManager->get(self::TEAMS_FIELD);

        if (empty($teams)) {
            return array();
        }

        $teams = preg_split("/\r\n|\n|\r/", $teams);

        foreach ($teams as &$team) {
            $team = trim($team);
        }

        return $teams;
    }

    /**
     * @return string
     */
    public function getOrganization()
    {
        return $this->configManager->get(self::ORGANIZATION_FIELD);
    }
}
