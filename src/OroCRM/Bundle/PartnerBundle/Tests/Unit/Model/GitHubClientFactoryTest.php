<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Model;

use OroCRM\Bundle\PartnerBundle\Model\GitHubClientFactory;

class GitHubClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateClient()
    {
        $factory = new GitHubClientFactory();
        $actual = $factory->createClient();
        $this->assertInstanceOf('Github\Client', $actual);
    }
}
