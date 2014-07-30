<?php

namespace OroCRM\Bundle\PartnerBundle\Model;

use Github\Client;

class GitHubClientFactory
{
    /**
     * @return Client
     */
    public function createClient()
    {
        return new Client();
    }
}
