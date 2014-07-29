<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Provider;

use OroCRM\Bundle\PartnerBundle\Provider\ConfigurationProvider;

class ConfigurationProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigurationProvider
     */
    protected $provider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configManager;

    protected function setUp()
    {
        $this->configManager = $this->getMockBuilder('Oro\Bundle\ConfigBundle\Config\ConfigManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->provider = new ConfigurationProvider($this->configManager);
    }

    public function testGetApiToken()
    {
        $expected = 'test_token';
        $this->configManager->expects($this->once())
            ->method('get')
            ->with(ConfigurationProvider::TOKEN_FIELD)
            ->will($this->returnValue($expected));
        $actual = $this->provider->getApiToken();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getRepositoriesDataProvider
     */
    public function testGetRepositories($repositories, $expected)
    {
        $this->configManager->expects($this->once())
            ->method('get')
            ->with(ConfigurationProvider::REPOSITORIES_FIELD)
            ->will($this->returnValue($repositories));
        $actual = $this->provider->getRepositories();
        $this->assertEquals($expected, $actual);
    }

    public function getRepositoriesDataProvider()
    {
        $repository = 'firstRepo';
        $owner = 'firstOwner';
        $secondRepository = 'secondRepo';
        $secondOwner = 'secondOwner';
        return array(
            'repositories correctly explode' => array(
                'repositories' => "{$owner}/{$repository}\r{$secondOwner}/{$secondRepository}",
                'expected' => array(
                    array(
                        'owner' => $owner,
                        'name'  => $repository
                    ),
                    array(
                        'owner' => $secondOwner,
                        'name'  => $secondRepository
                    ),
                )
            ),
            'repositories correctly explode if spaces presented' => array(
                'repositories' => "  {$owner}/{$repository}  \n  {$secondOwner}/{$secondRepository} ",
                'expected' => array(
                    array(
                        'owner' => $owner,
                        'name'  => $repository
                    ),
                    array(
                        'owner' => $secondOwner,
                        'name'  => $secondRepository
                    ),
                )
            ),
            'repositories correctly explode if git hub url presented' => array(
                'repositories' => "https://github.com/{$owner}/{$repository}\r\n{$secondOwner}/{$secondRepository} ",
                'expected' => array(
                    array(
                        'owner' => $owner,
                        'name'  => $repository
                    ),
                    array(
                        'owner' => $secondOwner,
                        'name'  => $secondRepository
                    ),
                )
            ),
            'repositories correctly explode if repository incorrect' => array(
                'repositories' => "https://github.com/{$owner}/\r\n{$secondOwner} ",
                'expected' => array(
                    array(
                        'owner' => $owner,
                        'name'  => ''
                    ),
                    array(
                        'owner' => $secondOwner,
                        'name'  => ''
                    ),
                )
            ),
            'repositories correctly explode if repositories ' => array(
                'repositories' => "https://github.com/{$owner}/\r\n{$secondOwner} ",
                'expected' => array(
                    array(
                        'owner' => $owner,
                        'name'  => ''
                    ),
                    array(
                        'owner' => $secondOwner,
                        'name'  => ''
                    ),
                )
            )
        );
    }
}
